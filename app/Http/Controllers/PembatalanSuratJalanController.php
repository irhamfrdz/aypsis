<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\Karyawan;
use App\Models\PembatalanSuratJalan;
use App\Models\RiwayatUtangSupir;
use App\Models\SaldoUtangSupir;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PembatalanSuratJalanController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }

    public function index(Request $request)
    {
        $query = PembatalanSuratJalan::with(['suratJalan', 'suratJalanBongkaran', 'akunCoa', 'akunPiutang', 'karyawan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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
        $queryReg = \App\Models\SuratJalan::select('id', DB::raw("'reguler' as tipe_sj"), 'created_at')
            ->where('status', '!=', 'cancelled');

        // 2. Bongkaran SJ Query
        $queryBong = \App\Models\SuratJalanBongkaran::select('id', DB::raw("'bongkaran' as tipe_sj"), 'created_at')
            ->where('status', '!=', 'cancelled');

        if ($request->filled('search_sj')) {
            $queryReg->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('pengirim', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%")
                    ->orWhereHas('supirKaryawan', function ($sq) use ($search) {
                        $sq->where('nama_panggilan', 'like', "%{$search}%")
                            ->orWhere('nama_lengkap', 'like', "%{$search}%");
                    });
            });

            $queryBong->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('pengirim', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%")
                    ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        $unionQuery = $queryReg->unionAll($queryBong);

        $perPage = 10;
        $paginatedIds = DB::table(DB::raw("({$unionQuery->toSql()}) as combined"))
            ->mergeBindings($unionQuery->getQuery())
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $items = $paginatedIds->items();

        // Group by tipe_sj to load Eloquent models in bulk
        $regulerIds = [];
        $bongkaranIds = [];
        foreach ($items as $item) {
            if ($item->tipe_sj === 'reguler') {
                $regulerIds[] = $item->id;
            } else {
                $bongkaranIds[] = $item->id;
            }
        }

        $regulerModels = [];
        if (! empty($regulerIds)) {
            $regulerModels = \App\Models\SuratJalan::with(['supirKaryawan', 'uangJalan'])
                ->whereIn('id', $regulerIds)
                ->get()
                ->keyBy('id');
        }

        $bongkaranModels = [];
        if (! empty($bongkaranIds)) {
            $bongkaranModels = \App\Models\SuratJalanBongkaran::with(['uangJalan'])
                ->whereIn('id', $bongkaranIds)
                ->get()
                ->keyBy('id');
        }

        $orderedItems = collect();
        foreach ($items as $item) {
            if ($item->tipe_sj === 'reguler') {
                if (isset($regulerModels[$item->id])) {
                    $model = $regulerModels[$item->id];
                    $model->tipe_sj = 'reguler';
                    $orderedItems->push($model);
                }
            } else {
                if (isset($bongkaranModels[$item->id])) {
                    $model = $bongkaranModels[$item->id];
                    $model->tipe_sj = 'bongkaran';
                    $model->no_surat_jalan = $model->nomor_surat_jalan;
                    $orderedItems->push($model);
                }
            }
        }

        $suratJalans = new \Illuminate\Pagination\LengthAwarePaginator(
            $orderedItems,
            $paginatedIds->total(),
            $paginatedIds->perPage(),
            $paginatedIds->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Bank options for searchable dropdown (same source as payment forms)
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();
        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);
        $akunPiutang = Coa::where('tipe_akun', 'LIKE', '%piutang%')
            ->orWhere('nama_akun', 'LIKE', '%piutang%')
            ->orderBy('nomor_akun')
            ->get(['id', 'nomor_akun', 'nama_akun']);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get(['id', 'nik', 'nama_lengkap', 'nama_panggilan']);

        return view('pembatalan-surat-jalan.create', compact('suratJalans', 'akunCoa', 'akunBiaya', 'akunPiutang', 'karyawans'));
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
            'metode_pembatalan' => 'required|in:kas,utang_karyawan',
            'karyawan_id' => 'nullable|required_if:metode_pembatalan,utang_karyawan|integer|exists:karyawans,id',
            'akun_piutang_id' => 'nullable|required_if:metode_pembatalan,utang_karyawan|integer|exists:akun_coa,id',
            'bank' => 'nullable|required_if:metode_pembatalan,kas|string|max:255|exists:akun_coa,nama_akun',
            'akun_coa_id' => 'required|integer|exists:akun_coa,id',
            'jenis_transaksi' => 'nullable|required_if:metode_pembatalan,kas|in:Debit,Kredit',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $akunBiaya = Coa::findOrFail($validated['akun_coa_id']);
        if ($validated['metode_pembatalan'] === 'kas' && $akunBiaya->nama_akun === $validated['bank']) {
            throw ValidationException::withMessages(['akun_coa_id' => 'Akun biaya harus berbeda dari akun Kas/Bank.']);
        }
        if ($validated['metode_pembatalan'] === 'utang_karyawan' && $akunBiaya->id === (int) $validated['akun_piutang_id']) {
            throw ValidationException::withMessages(['akun_piutang_id' => 'Akun piutang harus berbeda dari akun biaya.']);
        }

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

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $suratJalan, $noSuratJalan, $tipeSj) {
            // Create Cancel Record data
            $pblData = [
                'no_surat_jalan' => $noSuratJalan,
                'tipe_sj' => $tipeSj,
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal_kas' => $validated['tanggal_kas'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'metode_pembatalan' => $validated['metode_pembatalan'],
                'karyawan_id' => $validated['metode_pembatalan'] === 'utang_karyawan' ? $validated['karyawan_id'] : null,
                'akun_piutang_id' => $validated['metode_pembatalan'] === 'utang_karyawan' ? $validated['akun_piutang_id'] : null,
                'bank' => $validated['metode_pembatalan'] === 'kas' ? $validated['bank'] : null,
                'akun_coa_id' => $validated['akun_coa_id'],
                'jenis_transaksi' => $validated['metode_pembatalan'] === 'kas' ? $validated['jenis_transaksi'] : 'Debit',
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

            if ($pbl->metode_pembatalan === 'utang_karyawan') {
                $this->recordEmployeeDebt($pbl);
            }

            // Hapus surat jalan yang dibatalkan
            $suratJalan->delete();

            $this->recordCancellationJournal($pbl);

            Log::info('Double Entry Accounting recorded for Pembatalan Surat Jalan', [
                'nomor_pembayaran' => $pbl->nomor_pembayaran,
                'no_surat_jalan' => $noSuratJalan,
                'total' => $pbl->total_tagihan_setelah_penyesuaian,
                'bank' => $pbl->bank,
                'akun_coa_id' => $pbl->akun_coa_id,
            ]);
        });

        $message = $validated['metode_pembatalan'] === 'utang_karyawan'
            ? 'Surat Jalan berhasil dibatalkan dan dicatat sebagai utang karyawan tanpa menambah Kas Truck.'
            : 'Surat Jalan berhasil dibatalkan dan data terkait sudah dihapus.';

        return redirect()->route('pembatalan-surat-jalan.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        $pembatalanSuratJalan->load(['suratJalan', 'suratJalanBongkaran', 'akunCoa', 'akunPiutang', 'karyawan']);

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
        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);
        $akunPiutang = Coa::where('tipe_akun', 'LIKE', '%piutang%')
            ->orWhere('nama_akun', 'LIKE', '%piutang%')
            ->orderBy('nomor_akun')
            ->get(['id', 'nomor_akun', 'nama_akun']);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get(['id', 'nik', 'nama_lengkap', 'nama_panggilan']);

        return view('pembatalan-surat-jalan.edit', compact('pembatalanSuratJalan', 'akunCoa', 'akunBiaya', 'akunPiutang', 'karyawans'));
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
                DB::rollBack();

                return back()->with('error', 'Data ini sudah ada di jurnal COA.');
            }

            $this->recordCancellationJournal($pembatalan);

            DB::commit();

            return back()->with('success', 'Data berhasil disinkronkan ke jurnal COA.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncToCoa Pembatalan: '.$e->getMessage());

            return back()->with('error', 'Gagal sinkronisasi: '.$e->getMessage());
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
            'metode_pembatalan' => 'required|in:kas,utang_karyawan',
            'karyawan_id' => 'nullable|required_if:metode_pembatalan,utang_karyawan|integer|exists:karyawans,id',
            'akun_piutang_id' => 'nullable|required_if:metode_pembatalan,utang_karyawan|integer|exists:akun_coa,id',
            'bank' => 'nullable|required_if:metode_pembatalan,kas|string|max:255|exists:akun_coa,nama_akun',
            'akun_coa_id' => 'required|integer|exists:akun_coa,id',
            'jenis_transaksi' => 'nullable|required_if:metode_pembatalan,kas|in:Debit,Kredit',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $akunBiaya = Coa::findOrFail($validated['akun_coa_id']);
        if ($validated['metode_pembatalan'] === 'kas' && $akunBiaya->nama_akun === $validated['bank']) {
            throw ValidationException::withMessages(['akun_coa_id' => 'Akun biaya harus berbeda dari akun Kas/Bank.']);
        }
        if ($validated['metode_pembatalan'] === 'utang_karyawan' && $akunBiaya->id === (int) $validated['akun_piutang_id']) {
            throw ValidationException::withMessages(['akun_piutang_id' => 'Akun piutang harus berbeda dari akun biaya.']);
        }

        DB::beginTransaction();
        try {
            // Keep total_tagihan_setelah_penyesuaian in sync if not explicitly changed
            $totalPembayaran = (float) ($pembatalanSuratJalan->total_pembayaran ?? 0);
            $totalPenyesuaian = (float) ($validated['total_tagihan_penyesuaian'] ?? 0);
            $totalAkhir = (float) ($validated['total_tagihan_setelah_penyesuaian'] ?? ($totalPembayaran + $totalPenyesuaian));

            $nomorPembayaran = $pembatalanSuratJalan->nomor_pembayaran;
            $hasJournal = $nomorPembayaran && CoaTransaction::where('nomor_referensi', $nomorPembayaran)->exists();
            if ($pembatalanSuratJalan->metode_pembatalan === 'utang_karyawan') {
                $this->reverseEmployeeDebt($pembatalanSuratJalan);
            }
            if ($hasJournal && ! $this->coaTransactionService->deleteTransactionByReference($nomorPembayaran)) {
                throw new \RuntimeException('Jurnal COA lama gagal dihapus.');
            }

            $pembatalanSuratJalan->update([
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal_kas' => $validated['tanggal_kas'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'metode_pembatalan' => $validated['metode_pembatalan'],
                'karyawan_id' => $validated['metode_pembatalan'] === 'utang_karyawan' ? $validated['karyawan_id'] : null,
                'akun_piutang_id' => $validated['metode_pembatalan'] === 'utang_karyawan' ? $validated['akun_piutang_id'] : null,
                'bank' => $validated['metode_pembatalan'] === 'kas' ? $validated['bank'] : null,
                'akun_coa_id' => $validated['akun_coa_id'],
                'jenis_transaksi' => $validated['metode_pembatalan'] === 'kas' ? $validated['jenis_transaksi'] : 'Debit',
                'total_tagihan_penyesuaian' => $validated['total_tagihan_penyesuaian'] ?? 0,
                'total_tagihan_setelah_penyesuaian' => $totalAkhir,
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'alasan_batal' => $validated['alasan_batal'],
                'updated_by' => auth()->id(),
            ]);

            if ($pembatalanSuratJalan->metode_pembatalan === 'utang_karyawan') {
                $this->recordEmployeeDebt($pembatalanSuratJalan);
            }

            if ($hasJournal) {
                $this->recordCancellationJournal($pembatalanSuratJalan);
            }

            DB::commit();

            return redirect()->route('pembatalan-surat-jalan.index')->with('success', 'Catatan pembatalan dan jurnal COA berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Pembatalan SJ: '.$e->getMessage());

            return back()->with('error', 'Gagal memperbarui data: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembatalanSuratJalan $pembatalanSuratJalan)
    {
        return redirect()->route('pembatalan-surat-jalan.index')->with('error', 'Penghapusan transaksional dibatasi.');
    }

    private function recordCancellationJournal(PembatalanSuratJalan $pembatalan): void
    {
        $akunBiaya = $pembatalan->akunCoa()->first();
        if ($pembatalan->akun_coa_id && ! $akunBiaya) {
            throw new \RuntimeException('Akun COA yang dipilih tidak ditemukan.');
        }

        $biayaEntry = [
            'nama_akun' => $akunBiaya?->nama_akun ?? 'Biaya Uang Jalan Muat',
            'jumlah' => (float) ($pembatalan->total_tagihan_setelah_penyesuaian ?? $pembatalan->total_pembayaran),
        ];
        if ($akunBiaya) {
            $biayaEntry['coa_id'] = $akunBiaya->id;
        }

        if ($pembatalan->metode_pembatalan === 'utang_karyawan') {
            $akunPiutang = $pembatalan->akunPiutang()->first();
            if (! $akunPiutang) {
                throw new \RuntimeException('Akun piutang karyawan tidak ditemukan.');
            }
            $debit = [
                'coa_id' => $akunPiutang->id,
                'nama_akun' => $akunPiutang->nama_akun,
                'jumlah' => $biayaEntry['jumlah'],
            ];
            $kredit = $biayaEntry;
        } else {
            $bankEntry = [
                'nama_akun' => $pembatalan->bank,
                'jumlah' => $biayaEntry['jumlah'],
            ];
            $debit = $pembatalan->jenis_transaksi === 'Debit' ? $bankEntry : $biayaEntry;
            $kredit = $pembatalan->jenis_transaksi === 'Debit' ? $biayaEntry : $bankEntry;
        }
        $keterangan = 'Pembatalan SJ '.$pembatalan->no_surat_jalan.' - '.$pembatalan->nomor_pembayaran;
        if ($pembatalan->keterangan) {
            $keterangan .= ' | '.$pembatalan->keterangan;
        }

        if (! $this->coaTransactionService->recordDoubleEntry(
            $debit,
            $kredit,
            $pembatalan->tanggal_pembayaran?->toDateString() ?? now()->toDateString(),
            $pembatalan->nomor_pembayaran,
            'Pembatalan Surat Jalan',
            $keterangan
        )) {
            throw new \RuntimeException('Jurnal COA pembatalan gagal dibuat.');
        }
    }

    private function recordEmployeeDebt(PembatalanSuratJalan $pembatalan): void
    {
        $nominal = (float) $pembatalan->total_tagihan_setelah_penyesuaian;
        $saldoUtang = SaldoUtangSupir::firstOrCreate(
            ['karyawan_id' => $pembatalan->karyawan_id],
            ['saldo' => 0]
        );
        $saldoUtang->increment('saldo', $nominal);

        RiwayatUtangSupir::create([
            'karyawan_id' => $pembatalan->karyawan_id,
            'tanggal' => $pembatalan->tanggal_pembayaran,
            'tipe' => 'penambahan',
            'nominal' => $nominal,
            'referensi' => $pembatalan->nomor_pembayaran,
            'keterangan' => 'Pembatalan Surat Jalan '.$pembatalan->no_surat_jalan,
        ]);
    }

    private function reverseEmployeeDebt(PembatalanSuratJalan $pembatalan): void
    {
        $riwayat = RiwayatUtangSupir::where('karyawan_id', $pembatalan->karyawan_id)
            ->where('referensi', $pembatalan->nomor_pembayaran)
            ->where('tipe', 'penambahan')
            ->get();
        $nominal = (float) $riwayat->sum('nominal');

        if ($nominal > 0) {
            SaldoUtangSupir::where('karyawan_id', $pembatalan->karyawan_id)->decrement('saldo', $nominal);
            RiwayatUtangSupir::whereKey($riwayat->pluck('id'))->delete();
        }
    }
}
