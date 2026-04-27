<?php

namespace App\Http\Controllers;

use App\Models\PembatalanSuratJalan;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Http\Controllers\Controller;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembatalanSuratJalanController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }
    public function index(Request $request)
    {
        $query = PembatalanSuratJalan::with(['suratJalan', 'suratJalanBongkaran']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('alasan_batal', 'like', "%{$search}%")
                  ->orWhere('nomor_pembayaran', 'like', "%{$search}%");
            });
        }

        $pembatalans = $query->orderBy('created_at', 'desc')
                            ->paginate(15)
                            ->withQueryString();

        // Check which cancellations are already in COA
        $syncedReferences = \App\Models\CoaTransaction::whereIn('nomor_referensi', $pembatalans->pluck('nomor_pembayaran'))
            ->pluck('nomor_referensi')
            ->toArray();

        foreach ($pembatalans as $pembatalan) {
            $pembatalan->is_synced = in_array($pembatalan->nomor_pembayaran, $syncedReferences);
        }

        return view('pembatalan-surat-jalan.index', compact('pembatalans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $search = $request->search_sj;

        // 1. Reguler SJ Query
        $queryReguler = \App\Models\SuratJalan::with(['supirKaryawan', 'uangJalan'])
            ->where('status', '!=', 'cancelled');
        
        // 2. Bongkaran SJ Query
        $queryBongkaran = \App\Models\SuratJalanBongkaran::with(['uangJalan'])
            ->where('status', '!=', 'cancelled');

        if ($request->filled('search_sj')) {
            $queryReguler->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhereHas('supirKaryawan', function ($sq) use ($search) {
                      $sq->where('nama_panggilan', 'like', "%{$search}%")
                         ->orWhere('nama_lengkap', 'like', "%{$search}%");
                  });
            });

            $queryBongkaran->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        // Get both and tag them
        $reguler = $queryReguler->get()->map(function($sj) {
            $sj->tipe_sj = 'reguler';
            return $sj;
        });

        $bongkaran = $queryBongkaran->get()->map(function($sj) {
            $sj->tipe_sj = 'bongkaran';
            // Alias for consistency with Regulr
            $sj->no_surat_jalan = $sj->nomor_surat_jalan;
            return $sj;
        });

        // Merge and Sort
        $combined = $reguler->merge($bongkaran)->sortByDesc('created_at');

        // Manual Pagination
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $combined->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $suratJalans = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $combined->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Bank options for searchable dropdown (same source as payment forms)
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        return view('pembatalan-surat-jalan.create', compact('suratJalans', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'surat_jalan_id' => 'required',
            'tipe_sj' => 'required|in:reguler,bongkaran',
            'alasan_batal' => 'required|string',
            'nomor_pembayaran' => 'nullable|string|max:255',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_kas' => 'required|date',
            'tanggal_pembayaran' => 'required|date',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Fallback generation if client-side generator did not run
        if (empty($validated['nomor_pembayaran'])) {
            $now = now();
            $validated['nomor_pembayaran'] = sprintf(
                'PBL-%s-%s-%06d',
                $now->format('m'),
                $now->format('y'),
                random_int(1, 999999)
            );
        }

        $tipeSj = $validated['tipe_sj'];
        if ($tipeSj === 'reguler') {
            $suratJalan = \App\Models\SuratJalan::findOrFail($validated['surat_jalan_id']);
            $noSuratJalan = $suratJalan->no_surat_jalan;
        } else {
            $suratJalan = \App\Models\SuratJalanBongkaran::findOrFail($validated['surat_jalan_id']);
            $noSuratJalan = $suratJalan->nomor_surat_jalan;
        }

        if ($suratJalan->status === 'cancelled') {
            return redirect()->back()->with('error', 'Surat Jalan sudah dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($validated, $suratJalan, $noSuratJalan, $tipeSj) {
            // Create Cancel Record data
            $pblData = [
                'no_surat_jalan' => $noSuratJalan,
                'tipe_sj' => $tipeSj,
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal_kas' => $validated['tanggal_kas'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'bank' => $validated['bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'total_pembayaran' => $validated['total_pembayaran'],
                'total_tagihan_penyesuaian' => $validated['total_tagihan_penyesuaian'] ?? 0,
                'total_tagihan_setelah_penyesuaian' => $validated['total_tagihan_setelah_penyesuaian'],
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'alasan_batal' => $validated['alasan_batal'],
                'status' => 'approved', // auto-approve to cancel immediately
                'created_by' => auth()->id(),
            ];

            if ($tipeSj === 'reguler') {
                $pblData['surat_jalan_id'] = $suratJalan->id;

                // Hapus data prospek yang terkait
                \App\Models\Prospek::where('surat_jalan_id', $suratJalan->id)
                    ->orWhere('no_surat_jalan', $noSuratJalan)
                    ->delete();

                // Hapus data tanda terima yang terkait
                \App\Models\TandaTerima::where('surat_jalan_id', $suratJalan->id)
                    ->orWhere('no_surat_jalan', $noSuratJalan)
                    ->delete();

                // Hapus data uang jalan yang terkait (unified table)
                $uangJalan = \App\Models\UangJalan::where('surat_jalan_id', $suratJalan->id)->first();
                if ($uangJalan) {
                    // Hapus juga adjustment terkait jika ada
                    $uangJalan->adjustments()->delete();
                    $uangJalan->delete();
                }
            } else {
                $pblData['surat_jalan_bongkaran_id'] = $suratJalan->id;
                
                // Hapus data uang jalan yang terkait (bongkaran)
                $uangJalan = \App\Models\UangJalan::where('surat_jalan_bongkaran_id', $suratJalan->id)->first();
                if ($uangJalan) {
                    $uangJalan->adjustments()->delete();
                    $uangJalan->delete();
                }
            }

            $pbl = PembatalanSuratJalan::create($pblData);

            // Hapus surat jalan yang dibatalkan
            $suratJalan->delete();

            // ==========================================
            // DOUBLE BOOK ACCOUNTING LOGIC (CORE)
            // ==========================================
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'] ?? 'Debit';
            $noPbl = $validated['nomor_pembayaran'];
            $keterangan = "Pembatalan SJ " . $noSuratJalan . " - " . $noPbl;
            if (!empty($validated['keterangan'])) {
                $keterangan .= " | " . $validated['keterangan'];
            }

            // Tentukan apakah bank di-debit atau di-kredit berdasarkan jenis transaksi
            if ($jenisTransaksi == 'Debit') {
                // Jenis Debit: Bank bertambah (Debit), Biaya Uang Jalan berkurang (Kredit)
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran], // DEBIT Bank
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran], // KREDIT Biaya
                    $validated['tanggal_pembayaran'],
                    $noPbl,
                    'Pembatalan Surat Jalan',
                    $keterangan
                );
            } else {
                // Jenis Kredit: Biaya Uang Jalan bertambah (Debit), Bank berkurang (Kredit)
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran], // DEBIT Biaya
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran], // KREDIT Bank
                    $validated['tanggal_pembayaran'],
                    $noPbl,
                    'Pembatalan Surat Jalan',
                    $keterangan
                );
            }

            Log::info('Double Entry Accounting recorded for Pembatalan Surat Jalan', [
                'nomor_pembayaran' => $noPbl,
                'no_surat_jalan' => $noSuratJalan,
                'total' => $totalPembayaran,
                'bank' => $bankName
            ]);
        });

        return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Surat Jalan berhasil dibatalkan dan data terkait sudah dihapus.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $pembatalanSuratJalan->load(['suratJalan', 'suratJalanBongkaran']);
        return view('pembatalan-surat-jalan.show', compact('pembatalanSuratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        return view('pembatalan-surat-jalan.edit', compact('pembatalanSuratJalan', 'akunCoa'));
    }

    /**
     * Sinkronkan data lama ke jurnal COA jika belum ada.
     */
    public function syncToCoa(PembatalanSuratJalan $pembatalan)
    {
        DB::beginTransaction();
        try {
            // Check if transaction already exists in COA
            $existing = \App\Models\CoaTransaction::where('nomor_referensi', $pembatalan->nomor_pembayaran)->first();
            if ($existing) {
                return back()->with('error', 'Data ini sudah ada di jurnal COA.');
            }

            $totalPembayaran = $pembatalan->total_tagihan_setelah_penyesuaian ?? $pembatalan->total_pembayaran;
            $bankName = $pembatalan->bank;
            $jenisTransaksi = $pembatalan->jenis_transaksi ?? 'Debit';
            $noPbl = $pembatalan->nomor_pembayaran;
            $keterangan = "Pembatalan SJ " . $pembatalan->no_surat_jalan . " - " . $noPbl;
            if (!empty($pembatalan->keterangan)) {
                $keterangan .= " | " . $pembatalan->keterangan;
            }

            // Tentukan apakah bank di-debit atau di-kredit berdasarkan jenis transaksi
            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran],
                    $pembatalan->tanggal_pembayaran ? $pembatalan->tanggal_pembayaran->toDateString() : now()->toDateString(),
                    $noPbl,
                    'Pembatalan Surat Jalan',
                    $keterangan
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran],
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    $pembatalan->tanggal_pembayaran ? $pembatalan->tanggal_pembayaran->toDateString() : now()->toDateString(),
                    $noPbl,
                    'Pembatalan Surat Jalan',
                    $keterangan
                );
            }

            DB::commit();
            return back()->with('success', 'Data berhasil disinkronkan ke jurnal COA.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncToCoa Pembatalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $validated = $request->validate([
            'alasan_batal' => 'required|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_kas' => 'required|date',
            'tanggal_pembayaran' => 'required|date',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Keep total_tagihan_setelah_penyesuaian in sync if not explicitly changed
            $totalPembayaran = (float) ($pembatalanSuratJalan->total_pembayaran ?? 0);
            $totalPenyesuaian = (float) ($validated['total_tagihan_penyesuaian'] ?? 0);
            $totalAkhir = (float) ($validated['total_tagihan_setelah_penyesuaian'] ?? ($totalPembayaran + $totalPenyesuaian));

            $pembatalanSuratJalan->update([
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal_kas' => $validated['tanggal_kas'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'bank' => $validated['bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'total_tagihan_penyesuaian' => $validated['total_tagihan_penyesuaian'] ?? 0,
                'total_tagihan_setelah_penyesuaian' => $totalAkhir,
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'alasan_batal' => $validated['alasan_batal'],
                'updated_by' => auth()->id()
            ]);

            // Sync with CoaTransactions (Update date and reference if needed, though usually just date)
            if ($pembatalanSuratJalan->nomor_pembayaran) {
                CoaTransaction::where('nomor_referensi', $pembatalanSuratJalan->nomor_pembayaran)
                    ->update(['tanggal_transaksi' => $validated['tanggal_pembayaran']]);
            }

            DB::commit();
            return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Catatan pembatalan dan jurnal COA berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Pembatalan SJ: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        return redirect()->route('pembatalan-surat-jalan.index')->with('error', 'Penghapusan transaksional dibatasi.');
    }
}
