<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckpointKontainerKeluarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display branch selection page
     */
    public function index()
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        // Get all cabangs from database
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('checkpoint-kontainer-keluar.index', compact('cabangs'));
    }

    /**
     * Display checkpoint page for specific branch (warehouse selection)
     */
    public function checkpoint($cabangSlug)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        // Map slug to cabang name
        $cabangMap = [
            'jakarta' => 'Jakarta',
            'batam' => 'Batam',
            'tanjung-pinang' => 'Tanjung Pinang',
        ];

        $cabangNama = $cabangMap[$cabangSlug] ?? null;

        if (!$cabangNama) {
            return redirect()->route('checkpoint-kontainer-keluar.index')
                ->with('error', 'Cabang tidak ditemukan');
        }

        // Get gudangs by lokasi (cabang)
        $gudangs = \App\Models\Gudang::where('lokasi', $cabangNama)
            ->where('status', 'aktif')
            ->orderBy('nama_gudang')
            ->get();

        return view('checkpoint-kontainer-keluar.checkpoint', compact('gudangs', 'cabangNama', 'cabangSlug'));
    }

    /**
     * Display surat jalan list for specific gudang
     */
    public function showSuratJalan($cabangSlug, $gudangId)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        // Map slug to cabang name
        $cabangMap = [
            'jakarta' => 'Jakarta',
            'batam' => 'Batam',
            'tanjung-pinang' => 'Tanjung Pinang',
        ];

        $cabangNama = $cabangMap[$cabangSlug] ?? null;

        if (!$cabangNama) {
            return redirect()->route('checkpoint-kontainer-keluar.index')
                ->with('error', 'Cabang tidak ditemukan');
        }

        $gudang = \App\Models\Gudang::findOrFail($gudangId);

        // Get kontainers by gudangs_id
        $kontainers = \App\Models\Kontainer::where('gudangs_id', $gudangId)
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Get stock_kontainers by gudangs_id
        $stockKontainers = \App\Models\StockKontainer::where('gudangs_id', $gudangId)
            ->orderBy('nomor_seri_gabungan', 'asc')
            ->get();

        // Get surat jalans that haven't received tanda terima yet
        $suratJalans = SuratJalan::whereNull('tanggal_tanda_terima')
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->orderBy('no_surat_jalan', 'desc')
            ->get();

        // Get all active gudangs for tujuan dropdown
        $gudangs = \App\Models\Gudang::where('status', 'aktif')
            ->orderBy('lokasi')
            ->orderBy('nama_gudang')
            ->get();

        return view('checkpoint-kontainer-keluar.surat-jalan', compact('kontainers', 'stockKontainers', 'cabangNama', 'cabangSlug', 'gudang', 'suratJalans', 'gudangs'));
    }

    /**
     * Display history of checkpoint kontainer keluar
     */
    public function history(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        $query = SuratJalan::whereNotNull('tanggal_checkpoint')
            ->orderBy('tanggal_checkpoint', 'desc');

        // Filter by date range if provided
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_checkpoint', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_checkpoint', '<=', $request->tanggal_sampai);
        }

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        $suratJalans = $query->paginate(20);

        return view('checkpoint-kontainer-keluar.history', compact('suratJalans'));
    }

    /**
     * Process kontainer keluar
     */
    public function processKeluar(Request $request, $suratJalanId)
    {
        $this->authorize('checkpoint-kontainer-keluar-create');

        $request->validate([
            'tanggal_checkpoint' => 'required|date',
            'catatan_checkpoint' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalan::findOrFail($suratJalanId);

            // Update surat jalan
            $suratJalan->update([
                'tanggal_checkpoint' => $request->tanggal_checkpoint,
                'catatan_checkpoint' => $request->catatan_checkpoint,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kontainer berhasil keluar dari checkpoint'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses kontainer keluar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process bulk kontainer keluar
     */
    public function bulkKeluar(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-create');

        $request->validate([
            'surat_jalan_ids' => 'required|array',
            'surat_jalan_ids.*' => 'exists:surat_jalans,id',
            'tanggal_checkpoint' => 'required|date',
            'catatan_checkpoint' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $updated = SuratJalan::whereIn('id', $request->surat_jalan_ids)
                ->update([
                    'tanggal_checkpoint' => $request->tanggal_checkpoint,
                    'catatan_checkpoint' => $request->catatan_checkpoint,
                ]);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil memproses {$updated} kontainer keluar");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses kontainer keluar: ' . $e->getMessage());
        }
    }

    /**
     * Cancel kontainer keluar
     */
    public function cancelKeluar($suratJalanId)
    {
        $this->authorize('checkpoint-kontainer-keluar-delete');

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalan::findOrFail($suratJalanId);

            // Reset checkpoint data
            $suratJalan->update([
                'tanggal_checkpoint' => null,
                'catatan_checkpoint' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkpoint kontainer keluar berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan checkpoint: ' . $e->getMessage()
            ], 500);
        }
    }
}
