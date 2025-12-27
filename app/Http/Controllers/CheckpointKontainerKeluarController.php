<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckpointKontainerKeluarController extends Controller
{
    /**
     * Display a listing of containers ready for exit checkpoint.
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['kontainer', 'order'])
            ->whereNotNull('no_kontainer')
            ->where(function($q) {
                $q->where('status_checkpoint_keluar', '!=', 'sudah_keluar')
                  ->orWhereNull('status_checkpoint_keluar');
            });

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->sampai_tanggal);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'belum') {
                $query->whereNull('status_checkpoint_keluar');
            } else {
                $query->where('status_checkpoint_keluar', $request->status);
            }
        }

        $suratJalans = $query->orderBy('tanggal_surat_jalan', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(20)
                            ->withQueryString();

        // Also provide available stock kontainers and kontainers for modal dropdown
        $stockKontainers = StockKontainer::active()->orderBy('nomor_seri_gabungan')->get();
        $kontainers = Kontainer::where('status', '!=', 'inactive')->orderBy('nomor_seri_gabungan')->get();

        // Statistics
        $stats = [
            'total_pending' => SuratJalan::whereNotNull('no_kontainer')
                ->where(function($q) {
                    $q->whereNull('status_checkpoint_keluar')
                      ->orWhere('status_checkpoint_keluar', 'pending');
                })->count(),
            'total_keluar_hari_ini' => SuratJalan::whereNotNull('no_kontainer')
                ->where('status_checkpoint_keluar', 'sudah_keluar')
                ->whereDate('waktu_keluar', Carbon::today())
                ->count(),
            'total_keluar_bulan_ini' => SuratJalan::whereNotNull('no_kontainer')
                ->where('status_checkpoint_keluar', 'sudah_keluar')
                ->whereMonth('waktu_keluar', Carbon::now()->month)
                ->whereYear('waktu_keluar', Carbon::now()->year)
                ->count(),
        ];

        return view('checkpoint-kontainer-keluar.index', compact('suratJalans', 'stats', 'stockKontainers', 'kontainers'));
    }

    /**
     * Display history of containers that have exited.
     */
    public function history(Request $request)
    {
        $query = SuratJalan::with(['kontainer', 'order'])
            ->whereNotNull('no_kontainer')
            ->where('status_checkpoint_keluar', 'sudah_keluar');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('waktu_keluar', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('waktu_keluar', '<=', $request->sampai_tanggal);
        }

        $suratJalans = $query->orderBy('waktu_keluar', 'desc')
                            ->paginate(20)
                            ->withQueryString();

        return view('checkpoint-kontainer-keluar.history', compact('suratJalans'));
    }

    /**
     * Process container exit checkpoint.
     */
    public function processKeluar(Request $request, SuratJalan $suratJalan)
    {
        $request->validate([
            'catatan_keluar' => 'nullable|string|max:500',
            'selected_kontainer' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // If user selected a specific container from dropdown, map it and update SJ
            if ($request->filled('selected_kontainer')) {
                // Expecting format 'stock:ID' or 'kontainer:ID'
                [$type, $id] = explode(':', $request->selected_kontainer);
                if ($type === 'stock') {
                    $stock = StockKontainer::find($id);
                    if ($stock) {
                        $suratJalan->no_kontainer = $stock->nomor_seri_gabungan;
                        // mark stock kontainer as rented
                        $stock->update(['status' => 'rented']);
                    }
                } elseif ($type === 'kontainer') {
                    $kont = Kontainer::find($id);
                    if ($kont) {
                        $suratJalan->no_kontainer = $kont->nomor_seri_gabungan;
                        // We'll update kontainer status below
                    }
                }
            }

            $suratJalan->update([
                'status_checkpoint_keluar' => 'sudah_keluar',
                'waktu_keluar' => Carbon::now(),
                'catatan_keluar' => $request->catatan_keluar,
                'user_keluar_id' => Auth::id(),
            ]);

            // Update status kontainer jika perlu
            if ($suratJalan->no_kontainer) {
                $kontainer = Kontainer::where('nomor_seri_gabungan', $suratJalan->no_kontainer)->first();
                if ($kontainer) {
                    $kontainer->update([
                        'status' => 'Sedang Digunakan',
                        'lokasi_terakhir' => 'Dalam Perjalanan',
                    ]);
                }
            }

            DB::commit();

            Log::info('Checkpoint Kontainer Keluar', [
                'surat_jalan_id' => $suratJalan->id,
                'no_surat_jalan' => $suratJalan->no_surat_jalan,
                'no_kontainer' => $suratJalan->no_kontainer,
                'waktu_keluar' => $suratJalan->waktu_keluar,
                'user' => Auth::user()->name,
            ]);

            return redirect()->back()->with('success', 'Kontainer ' . $suratJalan->no_kontainer . ' berhasil dicatat keluar pada ' . Carbon::now()->format('d/m/Y H:i'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Checkpoint Kontainer Keluar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses checkpoint keluar: ' . $e->getMessage());
        }
    }

    /**
     * Process bulk container exit.
     */
    public function bulkKeluar(Request $request)
    {
        $request->validate([
            'surat_jalan_ids' => 'required|array|min:1',
            'surat_jalan_ids.*' => 'exists:surat_jalans,id',
            'catatan_keluar' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $count = 0;
            $waktuKeluar = Carbon::now();

            foreach ($request->surat_jalan_ids as $id) {
                $suratJalan = SuratJalan::find($id);
                if ($suratJalan && $suratJalan->status_checkpoint_keluar !== 'sudah_keluar') {
                    $suratJalan->update([
                        'status_checkpoint_keluar' => 'sudah_keluar',
                        'waktu_keluar' => $waktuKeluar,
                        'catatan_keluar' => $request->catatan_keluar,
                        'user_keluar_id' => Auth::id(),
                    ]);

                    // Update status kontainer
                    if ($suratJalan->no_kontainer) {
                        Kontainer::where('nomor_seri_gabungan', $suratJalan->no_kontainer)
                            ->update([
                                'status' => 'Sedang Digunakan',
                                'lokasi_terakhir' => 'Dalam Perjalanan',
                            ]);
                    }

                    $count++;
                }
            }

            DB::commit();

            Log::info('Bulk Checkpoint Kontainer Keluar', [
                'count' => $count,
                'waktu_keluar' => $waktuKeluar,
                'user' => Auth::user()->name,
            ]);

            return redirect()->back()->with('success', $count . ' kontainer berhasil dicatat keluar pada ' . $waktuKeluar->format('d/m/Y H:i'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Bulk Checkpoint Kontainer Keluar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses bulk checkpoint keluar: ' . $e->getMessage());
        }
    }

    /**
     * Cancel container exit (revert status).
     */
    public function cancelKeluar(Request $request, SuratJalan $suratJalan)
    {
        $request->validate([
            'alasan_cancel' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $suratJalan->update([
                'status_checkpoint_keluar' => null,
                'waktu_keluar' => null,
                'catatan_keluar' => 'DIBATALKAN: ' . $request->alasan_cancel . ' (oleh ' . Auth::user()->name . ' pada ' . Carbon::now()->format('d/m/Y H:i') . ')',
                'user_keluar_id' => null,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Status checkpoint keluar untuk kontainer ' . $suratJalan->no_kontainer . ' berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Cancel Checkpoint Keluar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan checkpoint keluar: ' . $e->getMessage());
        }
    }
}
