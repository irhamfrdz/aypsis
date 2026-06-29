<?php

namespace App\Http\Controllers;

use App\Models\SkInvoiceGrup;
use App\Models\SkKontainer;
use App\Models\SkSewa;
use App\Models\SkTagihanBulan;
use App\Models\SkTarifSewa;
use App\Models\SkTipeKontainer;
use App\Models\SkUkuranKontainer;
use App\Models\VendorKontainerSewa;
use App\Services\SewaKontainerBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SewaKontainerController extends Controller
{
    protected SewaKontainerBillingService $billingService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->billingService = new SewaKontainerBillingService;
    }

    /**
     * Main page with 4 tabs
     */
    public function index()
    {
        return view('sewa-kontainer.index');
    }

    // ============================================================
    //  DASHBOARD STATS
    // ============================================================

    public function getStats(): JsonResponse
    {
        $totalKontainers = SkKontainer::count();
        $activeRentals = SkSewa::where('status_sewa', 'Aktif')->count();

        $totalUnpaid = SkTagihanBulan::whereIn('status_bayar', ['Belum Ditagih', 'Belum Bayar', 'Pranota'])
            ->selectRaw('COALESCE(SUM(COALESCE(jumlah_tagihan_override, jumlah_tagihan_estimasi)), 0) as total')
            ->value('total');

        $totalPaid = SkTagihanBulan::where('status_bayar', 'Lunas')
            ->selectRaw('COALESCE(SUM(COALESCE(jumlah_tagihan_override, jumlah_tagihan_estimasi)), 0) as total')
            ->value('total');

        return response()->json([
            'total_kontainers' => $totalKontainers,
            'active_rentals' => $activeRentals,
            'total_unpaid' => (int) $totalUnpaid,
            'total_paid' => (int) $totalPaid,
        ]);
    }

    // ============================================================
    //  MASTER DATA APIs
    // ============================================================

    // --- Vendors ---
    public function getVendors(Request $request): JsonResponse
    {
        $q = VendorKontainerSewa::query();
        if ($request->search) {
            $q->where('name', 'like', "%{$request->search}%");
        }
        return response()->json($q->orderBy('name')->get());
    }

    public function storeVendor(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        $vendor = VendorKontainerSewa::create([
            'name' => trim($request->name),
            'npwp' => $request->npwp,
            'tax_ppn_percent' => $request->tax_ppn_percent ?? 11,
            'tax_pph_percent' => $request->tax_pph_percent ?? 2,
            'status_aktif' => true,
        ]);

        return response()->json(['success' => true, 'data' => $vendor, 'message' => "Vendor \"{$vendor->name}\" berhasil ditambahkan"]);
    }

    public function toggleVendorStatus(VendorKontainerSewa $vendor): JsonResponse
    {
        $vendor->update(['status_aktif' => ! $vendor->status_aktif]);
        $status = $vendor->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json(['success' => true, 'message' => "Vendor \"{$vendor->name}\" berhasil {$status}"]);
    }

    // --- Tipe Kontainer ---
    public function getTipes(): JsonResponse
    {
        return response()->json(SkTipeKontainer::orderBy('nama_tipe')->get());
    }

    public function storeTipe(Request $request): JsonResponse
    {
        $request->validate(['nama_tipe' => 'required|string|max:100']);

        $tipe = SkTipeKontainer::create([
            'nama_tipe' => trim($request->nama_tipe),
            'status_aktif' => true,
        ]);

        return response()->json(['success' => true, 'data' => $tipe, 'message' => "Tipe \"{$tipe->nama_tipe}\" berhasil ditambahkan"]);
    }

    public function toggleTipeStatus(SkTipeKontainer $tipe): JsonResponse
    {
        $tipe->update(['status_aktif' => ! $tipe->status_aktif]);
        $status = $tipe->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json(['success' => true, 'message' => "Tipe \"{$tipe->nama_tipe}\" berhasil {$status}"]);
    }

    // --- Ukuran Kontainer ---
    public function getUkurans(): JsonResponse
    {
        return response()->json(SkUkuranKontainer::orderBy('deskripsi_ukuran')->get());
    }

    public function storeUkuran(Request $request): JsonResponse
    {
        $raw = trim($request->deskripsi_ukuran);
        if (! $raw) {
            return response()->json(['success' => false, 'message' => 'Ukuran tidak boleh kosong'], 422);
        }

        // Auto-format: "20" -> "20'"
        $formatted = preg_match('/^\d+$/', $raw) ? "{$raw}'" : $raw;

        if (SkUkuranKontainer::where('deskripsi_ukuran', $formatted)->exists()) {
            return response()->json(['success' => false, 'message' => "Ukuran \"{$formatted}\" sudah ada"], 422);
        }

        $ukuran = SkUkuranKontainer::create([
            'deskripsi_ukuran' => $formatted,
            'status_aktif' => true,
        ]);

        return response()->json(['success' => true, 'data' => $ukuran, 'message' => "Ukuran \"{$ukuran->deskripsi_ukuran}\" berhasil disimpan"]);
    }

    public function toggleUkuranStatus(SkUkuranKontainer $ukuran): JsonResponse
    {
        $ukuran->update(['status_aktif' => ! $ukuran->status_aktif]);
        $status = $ukuran->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json(['success' => true, 'message' => "Ukuran \"{$ukuran->deskripsi_ukuran}\" berhasil {$status}"]);
    }

    // --- Kontainer ---
    public function getKontainers(Request $request): JsonResponse
    {
        $q = SkKontainer::with(['vendor', 'tipe', 'ukuran']);
        if ($request->search) {
            $search = $request->search;
            $q->where(function ($query) use ($search) {
                $query->where('no_kontainer', 'like', "%{$search}%")
                    ->orWhereHas('vendor', fn ($v) => $v->where('name', 'like', "%{$search}%"));
            });
        }

        return response()->json($q->orderBy('no_kontainer')->limit(150)->get());
    }

    public function storeKontainer(Request $request): JsonResponse
    {
        $request->validate([
            'no_kontainer' => 'required|string|max:50',
            'vendor_id' => 'required|exists:vendor_kontainer_sewas,id',
            'tipe_id' => 'required|exists:sk_tipe_kontainers,id',
            'ukuran_id' => 'required|exists:sk_ukuran_kontainers,id',
        ]);

        $cleanNo = strtoupper(preg_replace('/\s+/', '', trim($request->no_kontainer)));

        if (SkKontainer::where('no_kontainer', $cleanNo)->exists()) {
            return response()->json(['success' => false, 'message' => "Nomor Kontainer \"{$cleanNo}\" sudah terdaftar (Wajib Unik 100%)"], 422);
        }

        $kontainer = SkKontainer::create([
            'no_kontainer' => $cleanNo,
            'vendor_id' => $request->vendor_id,
            'tipe_id' => $request->tipe_id,
            'ukuran_id' => $request->ukuran_id,
            'status_aktif' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $kontainer->load(['vendor', 'tipe', 'ukuran']),
            'message' => "Kontainer \"{$cleanNo}\" berhasil terdaftar",
        ]);
    }

    public function toggleKontainerStatus(SkKontainer $kontainer): JsonResponse
    {
        $kontainer->update(['status_aktif' => ! $kontainer->status_aktif]);
        $status = $kontainer->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json(['success' => true, 'message' => "Kontainer \"{$kontainer->no_kontainer}\" berhasil {$status}"]);
    }

    // --- Tarif ---
    public function getTarifs(Request $request): JsonResponse
    {
        $q = SkTarifSewa::with(['vendor', 'tipe', 'ukuran']);
        if ($request->search) {
            $search = $request->search;
            $q->whereHas('vendor', fn ($v) => $v->where('name', 'like', "%{$search}%"));
        }

        return response()->json($q->orderByDesc('tanggal_mulai_berlaku')->limit(150)->get());
    }

    public function storeTarif(Request $request): JsonResponse
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendor_kontainer_sewas,id',
            'tipe_id' => 'required|exists:sk_tipe_kontainers,id',
            'ukuran_id' => 'required|exists:sk_ukuran_kontainers,id',
        ]);

        $monthlyRate = (int) ($request->tarif_bulanan ?? 0);
        $dailyRate = (int) ($request->tarif_harian ?? 0);

        if ($monthlyRate <= 0 && $dailyRate <= 0) {
            return response()->json(['success' => false, 'message' => 'Minimal salah satu dari Tarif Bulanan atau Tarif Harian harus diisi (> 0)'], 422);
        }

        $validDate = $request->tanggal_mulai_berlaku
            ? SewaKontainerBillingService::parseInputDate($request->tanggal_mulai_berlaku) ?? now()->format('Y-m-d')
            : now()->format('Y-m-d');

        $parsedEnd = $request->tanggal_akhir_berlaku
            ? SewaKontainerBillingService::parseInputDate($request->tanggal_akhir_berlaku)
            : null;

        // Auto-close previous active tarif for same vendor/tipe/ukuran
        $existingActive = SkTarifSewa::where('vendor_id', $request->vendor_id)
            ->where('tipe_id', $request->tipe_id)
            ->where('ukuran_id', $request->ukuran_id)
            ->whereNull('tanggal_akhir_berlaku')
            ->first();

        if ($existingActive) {
            $existingActive->update([
                'tanggal_akhir_berlaku' => \Carbon\Carbon::parse($validDate)->subDay()->format('Y-m-d'),
            ]);
        }

        $tarif = SkTarifSewa::create([
            'vendor_id' => $request->vendor_id,
            'tipe_id' => $request->tipe_id,
            'ukuran_id' => $request->ukuran_id,
            'tarif_bulanan' => $monthlyRate,
            'tarif_harian' => $dailyRate,
            'tanggal_mulai_berlaku' => $validDate,
            'tanggal_akhir_berlaku' => $parsedEnd,
            'status_aktif' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $tarif->load(['vendor', 'tipe', 'ukuran']),
            'message' => 'Tarif berhasil disimpan dalam sistem database',
        ]);
    }

    public function toggleTarifStatus(SkTarifSewa $tarif): JsonResponse
    {
        $tarif->update(['status_aktif' => ! $tarif->status_aktif]);
        $status = $tarif->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json(['success' => true, 'message' => "Tarif berhasil {$status}"]);
    }

    // ============================================================
    //  TRANSAKSI SEWA APIs
    // ============================================================

    public function getSewas(Request $request): JsonResponse
    {
        $q = SkSewa::with(['vendor', 'kontainer.tipe', 'kontainer.ukuran', 'tagihans']);
        if ($request->search) {
            $search = $request->search;
            $q->where(function ($query) use ($search) {
                $query->where('no_kontainer', 'like', "%{$search}%")
                    ->orWhereHas('vendor', fn ($v) => $v->where('name', 'like', "%{$search}%"));
            });
        }

        $sewas = $q->orderByDesc('tanggal_sewa')->limit(150)->get();

        // Enrich with billing data
        $result = $sewas->map(function ($sewa) {
            $tagihans = $sewa->tagihans;
            $totalEstimasi = $tagihans->sum(fn ($t) => $t->jumlah_tagihan_override ?? $t->jumlah_tagihan_estimasi);
            $totalOutstanding = $tagihans->where('status_bayar', 'Belum Ditagih')
                ->sum(fn ($t) => $t->jumlah_tagihan_override ?? $t->jumlah_tagihan_estimasi);

            $hasInvoices = $tagihans->where('status_bayar', '!=', 'Belum Ditagih')->isNotEmpty();
            $isFullyPaid = $tagihans->isNotEmpty() && $tagihans->every(fn ($t) => $t->status_bayar === 'Lunas');
            $hasUnpaid = $tagihans->whereIn('status_bayar', ['Belum Bayar', 'Pranota'])->isNotEmpty();

            $billingStatus = 'Belum Ditagih';
            if ($isFullyPaid) {
                $billingStatus = 'Lunas';
            } elseif ($hasUnpaid) {
                $billingStatus = 'Sudah Ditagih';
            } elseif ($hasInvoices) {
                $billingStatus = 'Bayar Parsial';
            }

            return array_merge($sewa->toArray(), [
                'total_estimasi' => $totalEstimasi,
                'total_outstanding' => $totalOutstanding,
                'billing_status' => $billingStatus,
                'has_invoices' => $hasInvoices,
            ]);
        });

        return response()->json($result);
    }

    public function storeSewa(Request $request): JsonResponse
    {
        $request->validate([
            'no_kontainer' => 'required|string|max:50',
            'tanggal_sewa' => 'required|string',
        ]);

        $validTglSewa = SewaKontainerBillingService::parseInputDate($request->tanggal_sewa);
        if (! $validTglSewa) {
            return response()->json(['success' => false, 'message' => 'Format Tanggal Sewa tidak valid'], 422);
        }

        $noKontainer = strtoupper(preg_replace('/\s+/', '', trim($request->no_kontainer)));

        // Check if container exists and get vendor
        $kontainer = SkKontainer::where('no_kontainer', $noKontainer)->first();
        if (! $kontainer) {
            return response()->json(['success' => false, 'message' => "Kontainer \"{$noKontainer}\" tidak ditemukan"], 422);
        }

        // Check active rental
        $activeRental = SkSewa::where('no_kontainer', $noKontainer)->where('status_sewa', 'Aktif')->first();
        if ($activeRental) {
            return response()->json([
                'success' => false,
                'message' => "Kontainer \"{$noKontainer}\" belum dikembalikan dari sewa sebelumnya (masih aktif)",
            ], 422);
        }

        // Duplicate check
        if (SkSewa::where('no_kontainer', $noKontainer)->where('tanggal_sewa', $validTglSewa)->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Duplikat: Transaksi sewa untuk \"{$noKontainer}\" pada tanggal tersebut sudah ada",
            ], 422);
        }

        $jenisTarif = $request->jenis_tarif ?? 'Bulanan';
        $tarifBulanan = (int) ($request->tarif_bulanan ?? 0);
        $tarifHarian = (int) ($request->tarif_harian ?? 0);

        if ($jenisTarif === 'Bulanan' && $tarifBulanan <= 0) {
            return response()->json(['success' => false, 'message' => 'Tarif Bulanan tidak boleh kosong/nol'], 422);
        }
        if ($jenisTarif === 'Harian' && $tarifHarian <= 0) {
            return response()->json(['success' => false, 'message' => 'Tarif Harian tidak boleh kosong/nol'], 422);
        }

        $sewa = SkSewa::create([
            'no_kontainer' => $noKontainer,
            'vendor_id' => $kontainer->vendor_id,
            'tanggal_sewa' => $validTglSewa,
            'tanggal_kembali' => null,
            'tarif_bulanan' => $tarifBulanan,
            'tarif_harian' => $tarifHarian,
            'jenis_tarif' => $jenisTarif,
            'status_sewa' => 'Aktif',
            'catatan' => $request->catatan,
            'non_ppn' => (bool) $request->non_ppn,
        ]);

        // Generate billing periods
        $this->billingService->syncBillingPeriods($sewa);

        return response()->json([
            'success' => true,
            'data' => $sewa->load(['vendor', 'kontainer.tipe', 'kontainer.ukuran', 'tagihans']),
            'message' => "Transaksi Sewa Kontainer \"{$noKontainer}\" sukses dibuat",
        ]);
    }

    public function returnSewa(Request $request, SkSewa $sewa): JsonResponse
    {
        if ($this->billingService->sewaHasInvoices($sewa)) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak boleh diproses pengembalian karena sudah ada tagihannya. Harus lepas tagihannya dulu.',
            ], 422);
        }

        $validTglKembali = SewaKontainerBillingService::parseInputDate($request->tanggal_kembali);
        if (! $validTglKembali) {
            return response()->json(['success' => false, 'message' => 'Format Tanggal Kembali tidak valid'], 422);
        }

        if ($validTglKembali < $sewa->tanggal_sewa->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'Tanggal Kembali tidak boleh mendahului Tanggal Sewa'], 422);
        }

        $sewa->update([
            'tanggal_kembali' => $validTglKembali,
            'status_sewa' => 'Selesai',
        ]);

        // Re-sync billing periods
        $this->billingService->syncBillingPeriods($sewa);

        return response()->json([
            'success' => true,
            'message' => "Kontainer {$sewa->no_kontainer} berhasil dikembalikan pada tanggal " . SewaKontainerBillingService::formatIndoDate($validTglKembali),
        ]);
    }

    public function updateSewa(Request $request, SkSewa $sewa): JsonResponse
    {
        if ($this->billingService->sewaHasInvoices($sewa)) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak boleh di-edit karena sudah ada tagihannya.',
            ], 422);
        }

        $validTglSewa = SewaKontainerBillingService::parseInputDate($request->tanggal_sewa);
        if (! $validTglSewa) {
            return response()->json(['success' => false, 'message' => 'Format Tanggal Sewa tidak valid'], 422);
        }

        $validTglKembali = null;
        if ($request->tanggal_kembali) {
            $validTglKembali = SewaKontainerBillingService::parseInputDate($request->tanggal_kembali);
            if (! $validTglKembali) {
                return response()->json(['success' => false, 'message' => 'Format Tanggal Kembali tidak valid'], 422);
            }
            if ($validTglKembali < $validTglSewa) {
                return response()->json(['success' => false, 'message' => 'Tanggal Kembali tidak boleh mendahului Tanggal Sewa'], 422);
            }
        }

        $sewa->update([
            'tanggal_sewa' => $validTglSewa,
            'tanggal_kembali' => $validTglKembali,
            'jenis_tarif' => $request->jenis_tarif ?? $sewa->jenis_tarif,
            'tarif_bulanan' => (int) ($request->tarif_bulanan ?? $sewa->tarif_bulanan),
            'tarif_harian' => (int) ($request->tarif_harian ?? $sewa->tarif_harian),
            'status_sewa' => $validTglKembali ? 'Selesai' : 'Aktif',
            'catatan' => $request->catatan,
            'non_ppn' => (bool) $request->non_ppn,
        ]);

        // Re-sync billing periods
        $this->billingService->syncBillingPeriods($sewa);

        return response()->json([
            'success' => true,
            'message' => "Kontainer {$sewa->no_kontainer} berhasil diperbarui",
        ]);
    }

    public function destroySewa(SkSewa $sewa): JsonResponse
    {
        if ($this->billingService->sewaHasInvoices($sewa)) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak boleh dihapus karena sudah ada tagihannya.',
            ], 422);
        }

        $noKontainer = $sewa->no_kontainer;
        $sewa->tagihans()->delete();
        $sewa->delete();

        return response()->json([
            'success' => true,
            'message' => "Transaksi sewa untuk Kontainer {$noKontainer} berhasil dihapus",
        ]);
    }

    // ============================================================
    //  TAGIHAN / BILLING APIs
    // ============================================================

    public function getTagihans(Request $request): JsonResponse
    {
        $q = SkTagihanBulan::with(['sewa.vendor', 'sewa.kontainer.tipe', 'sewa.kontainer.ukuran']);

        if ($request->sewa_id) {
            $q->where('sewa_id', $request->sewa_id);
        }
        if ($request->vendor_id) {
            $q->whereHas('sewa', fn ($s) => $s->where('vendor_id', $request->vendor_id));
        }
        if ($request->status_bayar) {
            $q->where('status_bayar', $request->status_bayar);
        }
        if ($request->search) {
            $search = $request->search;
            $q->where(function ($query) use ($search) {
                $query->where('nomor_invoice', 'like', "%{$search}%")
                    ->orWhere('nomor_pranota', 'like', "%{$search}%")
                    ->orWhere('kode_tagihan', 'like', "%{$search}%")
                    ->orWhereHas('sewa', fn ($s) => $s->where('no_kontainer', 'like', "%{$search}%"));
            });
        }

        return response()->json($q->orderByDesc('tanggal_awal')->limit(150)->get());
    }

    public function updateTagihan(Request $request, SkTagihanBulan $tagihan): JsonResponse
    {
        $updates = [];

        if ($request->has('status_bayar')) {
            $updates['status_bayar'] = $request->status_bayar;
        }
        if ($request->has('nomor_invoice')) {
            $updates['nomor_invoice'] = $request->nomor_invoice;
        }
        if ($request->has('tanggal_tagihan')) {
            $updates['tanggal_tagihan'] = $request->tanggal_tagihan
                ? SewaKontainerBillingService::parseInputDate($request->tanggal_tagihan) : null;
        }
        if ($request->has('tanggal_bayar')) {
            $updates['tanggal_bayar'] = $request->tanggal_bayar
                ? SewaKontainerBillingService::parseInputDate($request->tanggal_bayar) : null;
        }
        if ($request->has('nomor_pranota')) {
            $updates['nomor_pranota'] = $request->nomor_pranota;
        }
        if ($request->has('tanggal_pranota')) {
            $updates['tanggal_pranota'] = $request->tanggal_pranota
                ? SewaKontainerBillingService::parseInputDate($request->tanggal_pranota) : null;
        }
        if ($request->has('jumlah_tagihan_override')) {
            $updates['jumlah_tagihan_override'] = $request->jumlah_tagihan_override !== null && $request->jumlah_tagihan_override !== ''
                ? (int) $request->jumlah_tagihan_override : null;
        }
        if ($request->has('jumlah_bayar')) {
            $updates['jumlah_bayar'] = $request->jumlah_bayar !== null && $request->jumlah_bayar !== ''
                ? (int) $request->jumlah_bayar : null;
        }
        if ($request->has('ppn')) {
            $updates['ppn'] = $request->ppn !== null && $request->ppn !== '' ? (int) $request->ppn : null;
        }
        if ($request->has('pph')) {
            $updates['pph'] = $request->pph !== null && $request->pph !== '' ? (int) $request->pph : null;
        }
        if ($request->has('nomor_bayar')) {
            $updates['nomor_bayar'] = $request->nomor_bayar;
        }
        if ($request->has('keterangan_selisih')) {
            $updates['keterangan_selisih'] = $request->keterangan_selisih;
        }

        $tagihan->update($updates);

        return response()->json([
            'success' => true,
            'data' => $tagihan->fresh(),
            'message' => 'Tagihan berhasil diperbarui',
        ]);
    }

    // ============================================================
    //  BULK IMPORT
    // ============================================================

    public function bulkImport(Request $request): JsonResponse
    {
        $type = $request->type;
        $text = $request->text;

        if (! $text) {
            return response()->json(['success' => false, 'message' => 'Data tidak boleh kosong'], 422);
        }

        $lines = array_filter(array_map('trim', explode("\n", $text)), function ($line) {
            return $line && ! str_starts_with($line, '#');
        });

        $successCount = 0;
        $errors = [];

        foreach ($lines as $lineNum => $line) {
            try {
                switch ($type) {
                    case 'vendor':
                        $name = trim($line);
                        if (! VendorKontainerSewa::where('name', $name)->exists()) {
                            VendorKontainerSewa::create([
                                'name' => $name,
                                'tax_ppn_percent' => 11,
                                'tax_pph_percent' => 2,
                                'status_aktif' => true,
                            ]);
                            $successCount++;
                        }
                        break;

                    case 'tipe':
                        $nama = trim($line);
                        if (! SkTipeKontainer::where('nama_tipe', $nama)->exists()) {
                            SkTipeKontainer::create(['nama_tipe' => $nama, 'status_aktif' => true]);
                            $successCount++;
                        }
                        break;

                    case 'ukuran':
                        $raw = trim($line);
                        $formatted = preg_match('/^\d+$/', $raw) ? "{$raw}'" : $raw;
                        if (! SkUkuranKontainer::where('deskripsi_ukuran', $formatted)->exists()) {
                            SkUkuranKontainer::create(['deskripsi_ukuran' => $formatted, 'status_aktif' => true]);
                            $successCount++;
                        }
                        break;

                    case 'kontainer':
                        $parts = array_map('trim', explode(';', $line));
                        if (count($parts) < 4) {
                            $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => 'Format: NO_KONTAINER ; VENDOR ; TIPE ; UKURAN'];

                            continue 2;
                        }
                        $noKont = strtoupper(preg_replace('/\s+/', '', $parts[0]));
                        $vendorName = $parts[1];
                        $tipeName = $parts[2];
                        $ukuranVal = preg_match('/^\d+$/', $parts[3]) ? "{$parts[3]}'" : $parts[3];

                        $vendor = VendorKontainerSewa::where('name', 'like', "%{$vendorName}%")->first();
                        $tipe = SkTipeKontainer::where('nama_tipe', 'like', "%{$tipeName}%")->first();
                        $ukuran = SkUkuranKontainer::where('deskripsi_ukuran', $ukuranVal)->first();

                        if (! $vendor || ! $tipe || ! $ukuran) {
                            $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => 'Vendor/Tipe/Ukuran tidak ditemukan'];

                            continue 2;
                        }

                        if (! SkKontainer::where('no_kontainer', $noKont)->exists()) {
                            SkKontainer::create([
                                'no_kontainer' => $noKont,
                                'vendor_id' => $vendor->id,
                                'tipe_id' => $tipe->id,
                                'ukuran_id' => $ukuran->id,
                                'status_aktif' => true,
                            ]);
                            $successCount++;
                        }
                        break;

                    case 'tarif':
                        $parts = array_map('trim', explode(';', $line));
                        if (count($parts) < 6) {
                            $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => 'Format: VENDOR ; TIPE ; UKURAN ; TARIF_BULANAN ; TARIF_HARIAN ; TGL_MULAI'];

                            continue 2;
                        }

                        $vendor = VendorKontainerSewa::where('name', 'like', "%{$parts[0]}%")->first();
                        $tipe = SkTipeKontainer::where('nama_tipe', 'like', "%{$parts[1]}%")->first();
                        $ukuranVal = preg_match('/^\d+$/', $parts[2]) ? "{$parts[2]}'" : $parts[2];
                        $ukuran = SkUkuranKontainer::where('deskripsi_ukuran', $ukuranVal)->first();
                        $tglMulai = SewaKontainerBillingService::parseInputDate($parts[5]);

                        if (! $vendor || ! $tipe || ! $ukuran || ! $tglMulai) {
                            $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => 'Data tidak valid'];

                            continue 2;
                        }

                        SkTarifSewa::create([
                            'vendor_id' => $vendor->id,
                            'tipe_id' => $tipe->id,
                            'ukuran_id' => $ukuran->id,
                            'tarif_bulanan' => (int) $parts[3],
                            'tarif_harian' => (int) $parts[4],
                            'tanggal_mulai_berlaku' => $tglMulai,
                            'status_aktif' => true,
                        ]);
                        $successCount++;
                        break;

                    case 'sewa':
                        $parts = array_map('trim', explode(';', $line));
                        if (count($parts) < 5) {
                            $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => 'Format minimal 5 kolom'];

                            continue 2;
                        }

                        $noKont = strtoupper(preg_replace('/\s+/', '', $parts[0]));
                        $kontainer = SkKontainer::where('no_kontainer', $noKont)->first();
                        if (! $kontainer) {
                            $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => "Kontainer {$noKont} tidak ditemukan"];

                            continue 2;
                        }

                        $tglSewa = SewaKontainerBillingService::parseInputDate($parts[2]);
                        $tglKembali = ! empty($parts[3]) ? SewaKontainerBillingService::parseInputDate($parts[3]) : null;
                        $jenisTarif = strtolower($parts[4]) === 'harian' ? 'Harian' : 'Bulanan';
                        $nonPpn = isset($parts[5]) && in_array(strtolower($parts[5]), ['tidak', 'no', 'false', '0']);

                        if (! $tglSewa) {
                            // Update existing active sewa with return date
                            $activeSewa = SkSewa::where('no_kontainer', $noKont)->where('status_sewa', 'Aktif')->first();
                            if ($activeSewa && $tglKembali) {
                                $activeSewa->update([
                                    'tanggal_kembali' => $tglKembali,
                                    'status_sewa' => 'Selesai',
                                ]);
                                $this->billingService->syncBillingPeriods($activeSewa);
                                $successCount++;
                            }

                            continue 2;
                        }

                        // Look for active tarif
                        $tarif = SkTarifSewa::where('vendor_id', $kontainer->vendor_id)
                            ->where('tipe_id', $kontainer->tipe_id)
                            ->where('ukuran_id', $kontainer->ukuran_id)
                            ->whereNull('tanggal_akhir_berlaku')
                            ->first();

                        $sewa = SkSewa::create([
                            'no_kontainer' => $noKont,
                            'vendor_id' => $kontainer->vendor_id,
                            'tanggal_sewa' => $tglSewa,
                            'tanggal_kembali' => $tglKembali,
                            'tarif_bulanan' => $tarif->tarif_bulanan ?? 0,
                            'tarif_harian' => $tarif->tarif_harian ?? 0,
                            'jenis_tarif' => $jenisTarif,
                            'status_sewa' => $tglKembali ? 'Selesai' : 'Aktif',
                            'non_ppn' => $nonPpn,
                        ]);

                        $this->billingService->syncBillingPeriods($sewa);
                        $successCount++;
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = ['line' => $lineNum + 1, 'raw' => $line, 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'success' => true,
            'success_count' => $successCount,
            'errors' => $errors,
            'message' => "Berhasil mengimpor {$successCount} data" . (count($errors) > 0 ? ', ' . count($errors) . ' error' : ''),
        ]);
    }

    // ============================================================
    //  SYNC BILLING (manual trigger)
    // ============================================================

    public function syncAllBilling(): JsonResponse
    {
        $sewas = SkSewa::all();
        foreach ($sewas as $sewa) {
            $this->billingService->syncBillingPeriods($sewa);
        }

        return response()->json(['success' => true, 'message' => 'Billing periods berhasil di-sync untuk semua sewa']);
    }

    // ============================================================
    //  BACKUP RESTORE (JSON)
    // ============================================================
    public function restoreBackup(Request $request): JsonResponse
    {
        $request->validate([
            'backup_file' => 'required|file|mimetypes:application/json,text/plain'
        ]);

        // Tingkatkan batas waktu eksekusi agar tidak timeout saat parsing JSON besar
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $fileContent = file_get_contents($request->file('backup_file')->getRealPath());
        $data = json_decode($fileContent, true);

        if (!$data || !is_array($data)) {
            return response()->json(['success' => false, 'message' => 'Format file JSON tidak valid.'], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $vendorMap = [];
            $tipeMap = [];
            $ukuranMap = [];
            $tagihanMap = [];

            // 1. Customers -> Vendors
            if (isset($data['customers']) && is_array($data['customers'])) {
                foreach ($data['customers'] as $cust) {
                    $vendor = VendorKontainerSewa::updateOrCreate(
                        ['name' => trim($cust['nama_customer'])],
                        ['status_aktif' => $cust['status_aktif'] ?? true]
                    );
                    $vendorMap[$cust['id_customer']] = $vendor->id;
                }
            }

            // 2. Tipe Kontainer
            if (isset($data['tipes']) && is_array($data['tipes'])) {
                foreach ($data['tipes'] as $tipe) {
                    $t = SkTipeKontainer::updateOrCreate(
                        ['nama_tipe' => trim($tipe['nama_tipe'])],
                        ['status_aktif' => $tipe['status_aktif'] ?? true]
                    );
                    $tipeMap[$tipe['id_tipe']] = $t->id;
                }
            }

            // 3. Ukuran Kontainer
            if (isset($data['ukurans']) && is_array($data['ukurans'])) {
                foreach ($data['ukurans'] as $uk) {
                    $u = SkUkuranKontainer::updateOrCreate(
                        ['deskripsi_ukuran' => trim($uk['deskripsi_ukuran'])],
                        ['status_aktif' => $uk['status_aktif'] ?? true]
                    );
                    $ukuranMap[$uk['id_ukuran']] = $u->id;
                }
            }

            // 4. Master Kontainer
            if (isset($data['kontainers']) && is_array($data['kontainers'])) {
                foreach ($data['kontainers'] as $kon) {
                    if (!isset($vendorMap[$kon['id_customer']]) || !isset($tipeMap[$kon['id_tipe']]) || !isset($ukuranMap[$kon['id_ukuran']])) {
                        continue;
                    }
                    SkKontainer::updateOrCreate(
                        ['no_kontainer' => trim($kon['no_kontainer'])],
                        [
                            'vendor_id' => $vendorMap[$kon['id_customer']],
                            'tipe_id' => $tipeMap[$kon['id_tipe']],
                            'ukuran_id' => $ukuranMap[$kon['id_ukuran']],
                            'status_aktif' => $kon['status_aktif'] ?? true
                        ]
                    );
                }
            }

            // 5. Master Tarif
            if (isset($data['tarifs']) && is_array($data['tarifs'])) {
                foreach ($data['tarifs'] as $trf) {
                    if (!isset($vendorMap[$trf['id_customer']]) || !isset($tipeMap[$trf['id_tipe']]) || !isset($ukuranMap[$trf['id_ukuran']])) {
                        continue;
                    }
                    SkTarifSewa::updateOrCreate(
                        [
                            'vendor_id' => $vendorMap[$trf['id_customer']],
                            'tipe_id' => $tipeMap[$trf['id_tipe']],
                            'ukuran_id' => $ukuranMap[$trf['id_ukuran']],
                            'tanggal_mulai_berlaku' => $trf['tanggal_mulai_berlaku'],
                        ],
                        [
                            'tarif_bulanan' => $trf['tarif_bulanan'] ?? 0,
                            'tarif_harian' => $trf['tarif_harian'] ?? 0,
                            'tanggal_akhir_berlaku' => $trf['tanggal_akhir_berlaku'] ?? null,
                            'status_aktif' => $trf['status_aktif'] ?? true
                        ]
                    );
                }
            }

            // 6. Sewa
            if (isset($data['sewas']) && is_array($data['sewas'])) {
                foreach ($data['sewas'] as $sw) {
                    if (!isset($vendorMap[$sw['id_customer']])) {
                        continue;
                    }
                    $sewa = SkSewa::updateOrCreate(
                        [
                            'no_kontainer' => trim($sw['no_kontainer']),
                            'tanggal_sewa' => $sw['tanggal_sewa'],
                        ],
                        [
                            'vendor_id' => $vendorMap[$sw['id_customer']],
                            'tanggal_kembali' => $sw['tanggal_kembali'] ?? null,
                            'tarif_bulanan' => $sw['tarif_bulanan'] ?? 0,
                            'tarif_harian' => $sw['tarif_harian'] ?? 0,
                            'jenis_tarif' => $sw['jenis_tarif'] ?? 'Bulanan',
                            'status_sewa' => ($sw['status_sewa'] ?? '') === 'Selesai' ? 'Selesai' : 'Aktif',
                            'catatan' => $sw['catatan'] ?? null,
                            'non_ppn' => $sw['non_ppn'] ?? false,
                        ]
                    );
                    
                    // Generate tagihan for this sewa using Billing Service
                    $this->billingService->syncBillingPeriods($sewa);
                }
            }

            // 7. Payment Overrides
            if (isset($data['paymentOverrides']) && is_array($data['paymentOverrides'])) {
                $overrideKeys = array_keys($data['paymentOverrides']);
                $chunks = array_chunk($overrideKeys, 1000); // Batasi per chunk agar query tidak macet

                foreach ($chunks as $chunk) {
                    $tagihans = SkTagihanBulan::whereIn('kode_tagihan', $chunk)->get();
                    foreach ($tagihans as $tagihan) {
                        $override = $data['paymentOverrides'][$tagihan->kode_tagihan];
                        // Skip if nothing changed
                        if (
                            $tagihan->status_bayar !== ($override['status_bayar'] ?? 'Belum Ditagih') ||
                            $tagihan->jumlah_tagihan_override != ($override['jumlah_tagihan_override'] ?? null)
                        ) {
                            $tagihan->update([
                                'status_bayar' => $override['status_bayar'] ?? 'Belum Ditagih',
                                'tanggal_tagihan' => $override['tanggal_tagihan'] ?? null,
                                'tanggal_bayar' => $override['tanggal_bayar'] ?? null,
                                'nomor_invoice' => $override['nomor_invoice_grup'] ?? null,
                                'jumlah_tagihan_override' => $override['jumlah_tagihan_override'] ?? null,
                                'nomor_pranota' => $override['nomor_pranota'] ?? null,
                                'tanggal_pranota' => $override['tanggal_pranota'] ?? null,
                                'jumlah_bayar' => $override['jumlah_bayar'] ?? null,
                                'ppn' => $override['ppn'] ?? null,
                                'pph' => $override['pph'] ?? null,
                                'keterangan_selisih' => $override['keterangan_selisih'] ?? null,
                            ]);
                        }
                        $tagihanMap[$tagihan->kode_tagihan] = $tagihan->id;
                    }
                }
            }

            // 8. Invoices
            if (isset($data['invoices']) && is_array($data['invoices'])) {
                foreach ($data['invoices'] as $inv) {
                    if (!isset($vendorMap[$inv['id_customer']])) {
                        continue;
                    }
                    $invoiceGrup = SkInvoiceGrup::updateOrCreate(
                        ['nomor_invoice' => trim($inv['nomor_invoice'])],
                        [
                            'vendor_id' => $vendorMap[$inv['id_customer']],
                            'tanggal_invoice' => $inv['tanggal_invoice'],
                            'status_pembayaran' => $inv['status_pembayaran'] ?? 'Belum Bayar',
                            'deskripsi' => $inv['deskripsi'] ?? null,
                        ]
                    );

                    if (isset($inv['list_id_tagihan']) && is_array($inv['list_id_tagihan'])) {
                        $tagihanIds = [];
                        foreach ($inv['list_id_tagihan'] as $kode) {
                            if (isset($tagihanMap[$kode])) {
                                $tagihanIds[] = $tagihanMap[$kode];
                            } else {
                                $tb = SkTagihanBulan::where('kode_tagihan', $kode)->first();
                                if ($tb) $tagihanIds[] = $tb->id;
                            }
                        }
                        if (count($tagihanIds) > 0) {
                            $invoiceGrup->tagihans()->syncWithoutDetaching($tagihanIds);
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true, 'message' => 'Backup JSON berhasil dipulihkan secara permanen ke Database!']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memulihkan backup: ' . $e->getMessage()], 500);
        }
    }
}
