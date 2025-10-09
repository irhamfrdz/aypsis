<?php

namespace App\Http\Controllers;

use App\Models\PembayaranAktivitasLainnya;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PembayaranAktivitasLainnyaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembayaranAktivitasLainnya::with(['creator', 'bank']);

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pembayaran', 'like', '%' . $request->search . '%')
                  ->orWhere('aktivitas_pembayaran', 'like', '%' . $request->search . '%');
            });
        }

        $pembayaran = $query->latest()->paginate(20);

        return view('pembayaran-aktivitas-lainnya.index', compact('pembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Fetch bank/kas accounts from master COA
        $bankAccounts = Coa::where('tipe_akun', '=', 'Kas/Bank')
            ->orderBy('nomor_akun')
            ->get();

        return view('pembayaran-aktivitas-lainnya.create', compact('bankAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_pembayaran' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'pilih_bank' => 'required|exists:akun_coa,id',
            'aktivitas_pembayaran' => 'required|string|min:5|max:1000',
            'total_pembayaran' => 'required|numeric|min:0',
            'is_dp' => 'nullable|boolean'
        ], [
            'aktivitas_pembayaran.required' => 'Aktivitas pembayaran wajib diisi.',
            'aktivitas_pembayaran.min' => 'Aktivitas pembayaran minimal 5 karakter.',
            'aktivitas_pembayaran.max' => 'Aktivitas pembayaran maksimal 1000 karakter.',
            'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
            'pilih_bank.required' => 'Pilihan bank wajib dipilih.',
            'total_pembayaran.required' => 'Total pembayaran wajib diisi.',
            'total_pembayaran.min' => 'Total pembayaran harus lebih dari 0.'
        ]);

        try {
            // Get bank info dari COA
            $bankCoa = Coa::find($request->pilih_bank);

            // Generate nomor pembayaran jika kosong
            $nomorPembayaran = $request->nomor_pembayaran;
            if (!$nomorPembayaran) {
                $nomorPembayaran = PembayaranAktivitasLainnya::generateNomorPembayaranCoa($request->pilih_bank);
            }

            // Clean total pembayaran
            $totalPembayaran = is_numeric($request->total_pembayaran)
                ? $request->total_pembayaran
                : (float) str_replace(['.', ','], ['', '.'], $request->total_pembayaran);

            // Start database transaction
            DB::beginTransaction();

            // Simpan pembayaran
            $pembayaran = PembayaranAktivitasLainnya::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'total_pembayaran' => $totalPembayaran,
                'pilih_bank' => $request->pilih_bank,
                'aktivitas_pembayaran' => $request->aktivitas_pembayaran,
                'is_dp' => $request->has('is_dp') ? true : false,
                'created_by' => Auth::id(),
            ]);

            // Single-Entry: Update saldo bank (kurangi saldo karena pengeluaran)
            $bankCoa->decrement('saldo', $totalPembayaran);
            
            Log::info('Pembayaran aktivitas lainnya berhasil dibuat', [
                'nomor_pembayaran' => $nomorPembayaran,
                'bank_account' => $bankCoa->nama_akun,
                'saldo_before' => $bankCoa->saldo + $totalPembayaran,
                'saldo_after' => $bankCoa->saldo,
                'amount' => $totalPembayaran,
                'is_dp' => $request->has('is_dp')
            ]);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lainnya.index')
                ->with('success', 'Pembayaran berhasil disimpan dengan nomor: ' . $pembayaran->nomor_pembayaran . '. Saldo ' . $bankCoa->nama_akun . ' telah dikurangi sebesar Rp ' . number_format($totalPembayaran, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create pembayaran aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        return view('pembayaran-aktivitas-lainnya.show', [
            'pembayaran' => $pembayaranAktivitasLainnya
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        // Get bank accounts for dropdown
        $bankAccounts = Coa::where('tipe_akun', 'Bank/Kas')->get();

        return view('pembayaran-aktivitas-lainnya.edit', compact(
            'pembayaranAktivitasLainnya',
            'bankAccounts'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        $request->validate([
            'nomor_pembayaran' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'pilih_bank' => 'required|exists:akun_coa,id',
            'aktivitas_pembayaran' => 'required|string|min:5|max:1000',
            'total_pembayaran' => 'required|numeric|min:0',
            'is_dp' => 'nullable|boolean'
        ], [
            'aktivitas_pembayaran.required' => 'Aktivitas pembayaran wajib diisi.',
            'aktivitas_pembayaran.min' => 'Aktivitas pembayaran minimal 5 karakter.',
            'aktivitas_pembayaran.max' => 'Aktivitas pembayaran maksimal 1000 karakter.',
            'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
            'pilih_bank.required' => 'Pilihan bank wajib dipilih.',
            'total_pembayaran.required' => 'Total pembayaran wajib diisi.',
            'total_pembayaran.min' => 'Total pembayaran harus lebih dari 0.'
        ]);

        try {
            // Get old and new bank info
            $oldBankCoa = Coa::find($pembayaranAktivitasLainnya->pilih_bank);
            $newBankCoa = Coa::find($request->pilih_bank);

            // Clean total pembayaran
            $totalPembayaran = is_numeric($request->total_pembayaran)
                ? $request->total_pembayaran
                : (float) str_replace(['.', ','], ['', '.'], $request->total_pembayaran);

            $oldTotalPembayaran = (float) $pembayaranAktivitasLainnya->total_pembayaran;

            // Start database transaction
            DB::beginTransaction();

            // Reverse old bank transaction (tambah kembali saldo lama)
            if ($oldBankCoa) {
                $oldBankCoa->increment('saldo', $oldTotalPembayaran);
            }

            // Apply new bank transaction (kurangi saldo baru)
            $newBankCoa->decrement('saldo', $totalPembayaran);

            // Update pembayaran record
            $pembayaranAktivitasLainnya->update([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'total_pembayaran' => $totalPembayaran,
                'pilih_bank' => $request->pilih_bank,
                'aktivitas_pembayaran' => $request->aktivitas_pembayaran,
                'is_dp' => $request->has('is_dp') ? true : false,
            ]);

            Log::info('Pembayaran aktivitas lainnya berhasil diupdate', [
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'old_bank' => $oldBankCoa->nama_akun ?? 'N/A',
                'new_bank' => $newBankCoa->nama_akun,
                'old_amount' => $oldTotalPembayaran,
                'new_amount' => $totalPembayaran,
                'bank_changed' => $oldBankCoa->id !== $newBankCoa->id
            ]);

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lainnya.index')
                ->with('success', 'Pembayaran berhasil diupdate. Saldo bank telah disesuaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update pembayaran aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal mengupdate pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        try {
            // Get bank info for reversal
            $bankCoa = Coa::find($pembayaranAktivitasLainnya->pilih_bank);
            $totalPembayaran = (float) $pembayaranAktivitasLainnya->total_pembayaran;
            $nomorPembayaran = $pembayaranAktivitasLainnya->nomor_pembayaran;

            // Start database transaction
            DB::beginTransaction();

            // Single-Entry: Kembalikan saldo bank (tambah kembali karena pembayaran dibatalkan)
            if ($bankCoa) {
                $bankCoa->increment('saldo', $totalPembayaran);
                
                Log::info('Pembayaran aktivitas lainnya berhasil dihapus - saldo dikembalikan', [
                    'nomor_pembayaran' => $nomorPembayaran,
                    'bank_account' => $bankCoa->nama_akun,
                    'amount_restored' => $totalPembayaran,
                    'saldo_after' => $bankCoa->saldo
                ]);
            }

            // Delete the payment record
            $pembayaranAktivitasLainnya->delete();

            DB::commit();

            return redirect()->route('pembayaran-aktivitas-lainnya.index')
                ->with('success', 'Pembayaran berhasil dihapus dan saldo ' . ($bankCoa->nama_akun ?? 'bank') . ' telah dikembalikan sebesar Rp ' . number_format($totalPembayaran, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete pembayaran aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }





    /**
     * Generate nomor pembayaran preview (API endpoint)
     */
    public function generateNomorPreview(Request $request)
    {
        try {
            $coaId = $request->get('coa_id');

            if (!$coaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'COA ID is required'
                ], 400);
            }

            $coa = Coa::find($coaId);

            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'COA not found'
                ], 404);
            }

            $today = now();
            $tahun = $today->format('y'); // 2 digit year
            $bulan = $today->format('m'); // 2 digit month

            // Ambil kode_nomor dari COA sebagai kode bank (sama seperti pembayaran kontainer)
            $kodeBank = $coa->kode_nomor ?? '000';

            // Get next running number from master nomor terakhir (preview only, don't increment)
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->first();

            if (!$nomorTerakhir) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module nomor_pembayaran tidak ditemukan di master nomor terakhir'
                ], 404);
            }

            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $nomorPembayaran = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";

            return response()->json([
                'success' => true,
                'nomor_pembayaran' => $nomorPembayaran,
                'details' => [
                    'kode_bank' => $kodeBank,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'running_number' => $sequence
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print pembayaran
     */
    public function print(PembayaranAktivitasLainnya $pembayaranAktivitasLainnya)
    {
        return view('pembayaran-aktivitas-lainnya.print', compact('pembayaranAktivitasLainnya'));
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        $query = PembayaranAktivitasLainnya::with(['creator', 'bank']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                  ->orWhere('aktivitas_pembayaran', 'like', "%{$search}%");
            });
        }



        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->date_to);
        }

        $pembayaran = $query->with(['bank', 'creator'])->orderBy('created_at', 'desc')->get();

        // Simple CSV export
        $filename = 'pembayaran_aktivitas_lainnya_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($pembayaran) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'No',
                'Nomor Pembayaran',
                'Tanggal Pembayaran',
                'Total Pembayaran',
                'Bank/Kas',
                'Dibuat Oleh',
                'Aktivitas Pembayaran'
            ]);

            // Data
            foreach ($pembayaran as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->nomor_pembayaran,
                    $item->tanggal_pembayaran ? Carbon::parse($item->tanggal_pembayaran)->format('d/m/Y') : '-',
                    number_format((float) $item->total_pembayaran, 0, ',', '.'),
                    $item->bank->nama_akun ?? '-',
                    $item->creator->username ?? '-',
                    $item->aktivitas_pembayaran ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
