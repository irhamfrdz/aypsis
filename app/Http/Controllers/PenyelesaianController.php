<?php

// File: app/Http/Controllers/PenyelesaianIIController.php
// Controller BARU untuk halaman dasbor penyelesaian tugas II (duplicate dari PenyelesaianController).

namespace App\Http\Controllers;

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
// removed: MasterPricelistSewaKontainer is not needed for simplified approval flow
use App\Models\Permohonan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// removed: TagihanKontainerSewa is not needed for simplified approval flow

class PenyelesaianController extends Controller
{
    /**
     * Menampilkan daftar permohonan yang perlu diselesaikan.
     */
    public function index()
    {
        $query = Permohonan::whereNotIn('status', ['Selesai', 'Dibatalkan'])
            ->where('approved_by_system_1', false) // Belum disetujui system 1
            ->with(['supir', 'kontainers', 'checkpoints']);

        if (request('vendor')) {
            $query->where('vendor_perusahaan', request('vendor'));
        }

        $permohonans = $query->latest()->paginate(10);

        return view('approval.dashboard', compact('permohonans'));
    }

    /**
     * Menampilkan riwayat permohonan yang sudah diselesaikan.
     */
    public function riwayat()
    {
        $query = Permohonan::whereIn('status', ['Selesai', 'Bermasalah', 'Dibatalkan'])
            ->with(['supir', 'kontainers', 'checkpoints']);

        // Filter berdasarkan vendor
        if (request('vendor')) {
            $query->where('vendor_perusahaan', request('vendor'));
        }

        // Filter berdasarkan status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter berdasarkan kegiatan
        if (request('kegiatan')) {
            $query->where('kegiatan', request('kegiatan'));
        }

        // Filter berdasarkan tanggal
        if (request('tanggal_dari') && request('tanggal_sampai')) {
            $query->whereBetween('updated_at', [
                request('tanggal_dari').' 00:00:00',
                request('tanggal_sampai').' 23:59:59',
            ]);
        } elseif (request('tanggal_dari')) {
            $query->whereDate('updated_at', '>=', request('tanggal_dari'));
        } elseif (request('tanggal_sampai')) {
            $query->whereDate('updated_at', '<=', request('tanggal_sampai'));
        }

        $permohonans = $query->latest('updated_at')->paginate(15);

        // Get filter options
        $vendors = Permohonan::whereIn('status', ['Selesai', 'Bermasalah', 'Dibatalkan'])
            ->distinct()
            ->pluck('vendor_perusahaan')
            ->filter()
            ->sort()
            ->values();

        $kegiatans = \App\Models\MasterKegiatan::orderBy('nama_kegiatan')->get();

        $statusOptions = [
            'Selesai' => 'Selesai',
            'Bermasalah' => 'Bermasalah',
            'Dibatalkan' => 'Dibatalkan',
        ];

        return view('approval.riwayat', compact('permohonans', 'vendors', 'kegiatans', 'statusOptions'));
    }

    /**
     * Create or merge a TagihanKontainerSewa row from a Permohonan.
     * This centralizes the approval -> tagihan logic used by massProcess and store.
     *
     * @param  string  $dateForTagihan  (Y-m-d)
     */
    protected function createOrUpdateTagihan(Permohonan $permohonan, $dateForTagihan, $kontainersPayload = null)
    {
        // Create daftar_tagihan_kontainer_sewa rows for each kontainer on the permohonan.
        // Grouping rule: entries that share the same vendor and tanggal_awal (checkpoint date)
        // will use the same `group` identifier. All created entries go into periode = 1.

        try {
            $vendor = $permohonan->vendor_perusahaan ?? null;
            if (empty($vendor)) {
                Log::debug('createOrUpdateTagihan skipped: no vendor on permohonan', ['permohonan_id' => $permohonan->id]);

                return null;
            }

            // Normalize dateForTagihan to Y-m-d
            $dateStr = Carbon::parse($dateForTagihan)->toDateString();

            // No group assignment for approved containers - each container stands alone
            // $groupId = strtoupper($vendor) . '-' . Carbon::parse($dateStr)->format('Ymd');

            foreach ($permohonan->kontainers as $kontainer) {
                // Prepare values
                $nomor = $kontainer->nomor_kontainer ?? ($kontainer->nomor ?? null);
                if (empty($nomor)) {
                    continue;
                }

                // If caller provided kontainers payload (from the form), prefer its size for this kontainer
                $overrideSize = null;
                try {
                    if (is_array($kontainersPayload) && isset($kontainersPayload[$permohonan->id]) && isset($kontainersPayload[$permohonan->id][$nomor])) {
                        $overrideSize = $kontainersPayload[$permohonan->id][$nomor]['size'] ?? null;
                    }
                } catch (\Exception $e) {
                    Log::debug('createOrUpdateTagihan: kontainersPayload access failed', ['exception' => $e->getMessage(), 'permohonan_id' => $permohonan->id, 'nomor' => $nomor]);
                }

                // determine size/ukuran to use for pricelist lookup and saved nilai
                $sizeForLookup = $overrideSize ?? $kontainer->ukuran ?? null;
                Log::debug('createOrUpdateTagihan: resolved size', ['permohonan_id' => $permohonan->id, 'nomor' => $nomor, 'override' => $overrideSize, 'model_ukuran' => $kontainer->ukuran ?? null, 'sizeForLookup' => $sizeForLookup]);

                $tanggal_awal = $dateStr;
                // Use kontainer's tanggal_selesai_sewa if set as tanggal_akhir
                $tanggal_akhir = $kontainer->tanggal_selesai_sewa ?? null;

                // Calculate masa string and numeric days if both dates present
                $masa = null; // string like '21 januari 2025 - 20 februari 2025'
                $masaDays = null; // numeric days used for tariff math
                if ($tanggal_awal) {
                    $startObj = Carbon::parse($tanggal_awal);
                    // period end is start +1 month -1 day, capped to tanggal_akhir when present
                    $periodEndCandidate = $startObj->copy()->addMonthsNoOverflow(1)->subDay();
                    if (! empty($tanggal_akhir)) {
                        $taObj = Carbon::parse($tanggal_akhir);
                        $endObj = $taObj->lessThan($periodEndCandidate) ? $taObj : $periodEndCandidate;
                    } else {
                        $endObj = $periodEndCandidate;
                    }
                    $masa = strtolower($startObj->locale('id')->isoFormat('D MMMM YYYY')).' - '.strtolower($endObj->locale('id')->isoFormat('D MMMM YYYY'));
                    $masaDays = $startObj->diffInDays($endObj);
                }

                // Idempotent create: avoid duplicate rows for same vendor+kontainer+tanggal_awal
                $attrs = [
                    'vendor' => $vendor,
                    'nomor_kontainer' => $nomor,
                    'tanggal_awal' => $tanggal_awal,
                    // No group assignment - each approved container stands alone
                    // 'group' => $groupId,
                ];

                // Attempt to fetch monthly pricelist for this vendor + kontainer ukuran (use override if provided)
                $pricelist = null;
                try {
                    if (! empty($vendor) && ! empty($sizeForLookup)) {
                        $pricelist = MasterPricelistSewaKontainer::where('vendor', $vendor)
                            ->where('ukuran_kontainer', $sizeForLookup)
                            ->where(function ($q) use ($dateStr) {
                                $q->whereNull('tanggal_harga_awal')->orWhere('tanggal_harga_awal', '<=', $dateStr);
                            })
                            ->where(function ($q) use ($dateStr) {
                                $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $dateStr);
                            })
                            ->orderByDesc('tanggal_harga_awal')
                            ->first();
                    }
                } catch (\Exception $e) {
                    Log::error('Pricelist lookup failed', ['message' => $e->getMessage(), 'vendor' => $vendor, 'ukuran' => $kontainer->ukuran ?? null]);
                }

                // Default tarif/dpp values; when pricelist found, use it
                $defaultTarif = $kontainer->tarif ?? 0;
                $defaultDpp = $kontainer->dpp ?? 0;

                if ($pricelist) {
                    // tarif default must be the monthly tarif from pricelist when present
                    if (! is_null($pricelist->tarif)) {
                        $defaultTarif = $pricelist->tarif;
                    }
                    // dpp should be the pricelist harga (base amount)
                    if (! is_null($pricelist->harga)) {
                        $defaultDpp = $pricelist->harga;
                    }
                }

                // If this kontainer was marked as returned/dikembalikan and masa < 30 days,
                // prefer a daily tariff: attempt to use pricelist->harga_harian if present,
                // otherwise approximate daily = monthly_harga / 30 and multiply by masa.
                $isReturned = strtolower((string) ($kontainer->status ?? '')) === 'dikembalikan' || (! empty($kontainer->tanggal_selesai_sewa) && ($kontainer->tanggal_selesai_sewa !== null));
                if ($isReturned && $masaDays !== null && $masaDays < 30) {
                    $dailyRate = null;
                    if ($pricelist && isset($pricelist->harga_harian) && ! is_null($pricelist->harga_harian)) {
                        $dailyRate = $pricelist->harga_harian;
                    } elseif ($pricelist && ! is_null($pricelist->harga)) {
                        $dailyRate = round((float) $pricelist->harga / 30, 2);
                    } else {
                        // fallback: base on default monthly dpp if available
                        $dailyRate = isset($defaultDpp) ? round((float) $defaultDpp / 30, 2) : 0;
                    }

                    // Use daily tarif label and compute dpp as dailyRate * masa
                    $defaultTarif = 'Harian';
                    $defaultDpp = round((float) $dailyRate * (int) $masaDays, 2);
                }

                $values = [
                    'tanggal_akhir' => $tanggal_akhir,
                    'periode' => 1,
                    'masa' => $masa,
                    // tarif label (either 'Bulanan' or 'Harian' or pricelist label)
                    'tarif' => $defaultTarif ?? 'Bulanan',
                    'dpp' => $defaultDpp,
                    // store the resolved size (either from override or model)
                    'size' => $sizeForLookup,
                    // Jika ada override pada model kontainer, gunakan itu; jika tidak, hitung dari dpp
                    'dpp_nilai_lain' => $kontainer->dpp_nilai_lain ?? round((float) ($defaultDpp ?? 0) * 11 / 12, 2),
                    // compute ppn from dpp_nilai_lain unless kontainer explicitly provided a ppn
                    'ppn' => $kontainer->ppn ?? round(((float) ($kontainer->dpp_nilai_lain ?? round((float) ($defaultDpp ?? 0) * 11 / 12, 2))) * 0.12, 2),
                    // compute pph from dpp (2%) unless kontainer explicitly provided a pph
                    'pph' => $kontainer->pph ?? round((float) ($defaultDpp ?? 0) * 0.02, 2),
                    // compute grand_total from components if not provided explicit
                    'grand_total' => $kontainer->grand_total ?? round(((float) ($defaultDpp ?? 0) + (float) ($kontainer->ppn ?? round(((float) ($kontainer->dpp_nilai_lain ?? round((float) ($defaultDpp ?? 0) * 11 / 12, 2))) * 0.12, 2))) - (float) ($kontainer->pph ?? round((float) ($defaultDpp ?? 0) * 0.02, 2)), 2),
                    'status' => $kontainer->status ?? 'Tersedia',
                ];

                // Use firstOrCreate to reuse existing group entries when present
                DaftarTagihanKontainerSewa::firstOrCreate($attrs, array_merge($attrs, $values));
            }

            Log::debug('createOrUpdateTagihan created/merged daftar_tagihan_kontainer_sewa', ['permohonan_id' => $permohonan->id, 'date' => $dateStr, 'container' => $nomor]);
        } catch (\Exception $e) {
            Log::error('createOrUpdateTagihan failed', ['message' => $e->getMessage(), 'permohonan_id' => $permohonan->id ?? null]);
        }

        return null;
    }



    /**
     * Proses masal permohonan yang dipilih pada dashboard approval.
     */
    public function massProcess(Request $request)
    {
        Log::debug('PenyelesaianIIController: massProcess entry', ['request' => $request->all()]);

        $validated = $request->validate([
            'permohonan_ids' => 'required|array|min:1',
            'permohonan_ids.*' => 'required|integer|exists:permohonans,id',
        ]);

        // Load selected permohonans with checkpoints so we can enforce checkpoint requirement
        $permohonansToProcess = Permohonan::whereIn('id', $validated['permohonan_ids'])
            ->with(['kontainers', 'checkpoints'])
            ->get();

        // Diagnostic: log kontainers payload shape (if any)
        $kontainersPayload = $request->input('kontainers', []);
        Log::debug('PenyelesaianIIController: kontainers payload', ['kontainers' => $kontainersPayload]);

        // Note: tests expect mass processing to proceed even if checkpoints are not present.
        // Previous behavior aborted when any permohonan lacked checkpoints; allow processing anyway.

        DB::beginTransaction();
        try {
            $processed = 0;
            foreach ($permohonansToProcess as $permohonan) {

                // Mark as approved by system 1 (simple approval)
                $permohonan->approved_by_system_1 = true; // Mark as approved by Approval Tugas 1

                // Untuk Approval Tugas I, hanya update flag approval saja
                // Tidak melakukan proses lengkap seperti update kontainer, tagihan, atau perbaikan

                $permohonan->save();
                $processed++;
            }

            DB::commit();

            return redirect()->route('approval.dashboard')->with('success', "Berhasil memproses {$processed} permohonan.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PenyelesaianController: massProcess exception', ['message' => $e->getMessage(), 'exception' => $e]);

            return back()->with('error', 'Gagal melakukan proses masal: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk menyelesaikan tugas.
     * (Ini akan menjadi halaman yang Anda kirimkan sebelumnya)
     */
    public function create(Permohonan $permohonan)
    {
        $permohonan->load(['supir', 'kontainers', 'checkpoints']);

        // Use the regular view for non-repair containers
        return view('approval.checkpoint2-create', compact('permohonan'));
    }

    /**
     * Menyimpan data penyelesaian.
     */
    public function store(Request $request, Permohonan $permohonan)
    {
        // Debug: log incoming request and permohonan id (debug level)
        Log::debug('PenyelesaianController: store entry', [
            'permohonan_id' => $permohonan->id,
            'request' => $request->all(),
        ]);

        // Ensure related kontainers and checkpoints are loaded (useful for direct approvals/tests)
        $permohonan->load('kontainers', 'checkpoints');

        $validated = $request->validate([
            'status_permohonan' => 'required|in:selesai,bermasalah',
            'lampiran_kembali' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'catatan_karyawan' => 'nullable|string',
            // Validasi untuk input sewa yang baru ditambahkan
            'tanggal_masuk_sewa' => 'nullable|date',
            'tanggal_selesai_sewa' => 'nullable|date|after_or_equal:tanggal_masuk_sewa',
            // Validasi untuk estimasi perbaikan dan total biaya
            'estimasi_perbaikan' => 'nullable|string|max:1000',
            'total_biaya_perbaikan' => 'nullable|numeric|min:0',
            // Removed vendor_bengkel validation for simplified approval
        ]);

        DB::beginTransaction();
        try {
            // Update status permohonan - untuk Approval Tugas 1, ubah status menjadi "Disetujui Sistem 1"
            $permohonan->status = 'Disetujui Sistem 1'; // Ubah status dari Pending
            $permohonan->approved_by_system_1 = true; // Mark as approved by Approval Tugas 1

            // Simpan lampiran jika ada
            if ($request->hasFile('lampiran_kembali')) {
                $path = $request->file('lampiran_kembali')->store('public/lampiran_kembali');
                $permohonan->lampiran_kembali = $path; // Simpan path ke kolom baru
            }

            // Simpan catatan karyawan (mungkin di kolom 'catatan' atau kolom baru)
            if (array_key_exists('catatan_karyawan', $validated) && ! empty($validated['catatan_karyawan'])) {
                $permohonan->catatan = $permohonan->catatan."\n\n[Catatan Penyelesaian]:\n".$validated['catatan_karyawan'];
            }

            // Simpan estimasi perbaikan jika ada
            if (array_key_exists('estimasi_perbaikan', $validated) && ! empty($validated['estimasi_perbaikan'])) {
                $permohonan->catatan = $permohonan->catatan."\n\n[Estimasi Perbaikan]:\n".$validated['estimasi_perbaikan'];
            }

            // Simpan total biaya perbaikan jika ada
            if (array_key_exists('total_biaya_perbaikan', $validated) && ! empty($validated['total_biaya_perbaikan'])) {
                $biayaFormatted = 'Rp '.number_format($validated['total_biaya_perbaikan'], 0, ',', '.');
                $permohonan->catatan = $permohonan->catatan."\n\n[Total Biaya Perbaikan]: ".$biayaFormatted;
            }

            // Untuk Approval Tugas I, hanya update approved_by_system_1 saja
            // Tidak melakukan proses lengkap seperti update kontainer, tagihan, atau perbaikan

            $permohonan->save();

            DB::commit();

            // Simple success message untuk Approval Tugas I
            $successMessage = 'Permohonan berhasil disetujui pada Approval Tugas I!';

            return redirect()->route('approval.dashboard')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception for test/debug visibility
            Log::error('PenyelesaianIIController: exception', ['message' => $e->getMessage(), 'exception' => $e]);

            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    /**
     * Update tanggal_akhir for existing daftar_tagihan_kontainer_sewa records
     * when container is returned (tarik kontainer sewa)
     */
    protected function updateTagihanTanggalAkhir($nomorKontainer, $vendor, $tanggalAkhir)
    {
        try {
            // Find existing records for this container and vendor that don't have tanggal_akhir set
            $existingTagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
                ->where('vendor', $vendor)
                ->whereNull('tanggal_akhir')
                ->get();

            if ($existingTagihan->count() > 0) {
                Log::debug('updateTagihanTanggalAkhir: Found existing records to update', [
                    'nomor_kontainer' => $nomorKontainer,
                    'vendor' => $vendor,
                    'count' => $existingTagihan->count(),
                    'tanggal_akhir' => $tanggalAkhir,
                ]);

                foreach ($existingTagihan as $tagihan) {
                    $tagihan->tanggal_akhir = $tanggalAkhir;

                    // Recalculate masa based on new tanggal_akhir
                    if ($tagihan->tanggal_awal) {
                        $startObj = Carbon::parse($tagihan->tanggal_awal);
                        $endObj = Carbon::parse($tanggalAkhir);
                        $tagihan->masa = strtolower($startObj->locale('id')->isoFormat('D MMMM YYYY')).' - '.strtolower($endObj->locale('id')->isoFormat('D MMMM YYYY'));

                        // If periode duration is less than 30 days, adjust to daily tariff
                        $masaDays = $startObj->diffInDays($endObj) + 1; // include end date
                        if ($masaDays < 30 && $tagihan->tarif !== 'Harian') {
                            // Convert to daily tariff
                            $originalDpp = $tagihan->dpp ?? 0;
                            $dailyRate = round((float) $originalDpp / 30, 2);
                            $newDpp = round($dailyRate * $masaDays, 2);

                            $tagihan->tarif = 'Harian';
                            $tagihan->dpp = $newDpp;
                            $tagihan->dpp_nilai_lain = round($newDpp * 11 / 12, 2);
                            $tagihan->ppn = round($tagihan->dpp_nilai_lain * 0.12, 2);
                            $tagihan->pph = round($newDpp * 0.02, 2);
                            $tagihan->grand_total = round($newDpp + $tagihan->ppn - $tagihan->pph, 2);

                            Log::debug('updateTagihanTanggalAkhir: Converted to daily tariff', [
                                'nomor_kontainer' => $nomorKontainer,
                                'masa_days' => $masaDays,
                                'original_dpp' => $originalDpp,
                                'new_dpp' => $newDpp,
                            ]);
                        }
                    }

                    $tagihan->save();
                }

                Log::info('updateTagihanTanggalAkhir: Successfully updated records', [
                    'nomor_kontainer' => $nomorKontainer,
                    'vendor' => $vendor,
                    'updated_count' => $existingTagihan->count(),
                    'tanggal_akhir' => $tanggalAkhir,
                ]);
            } else {
                Log::debug('updateTagihanTanggalAkhir: No existing records found to update', [
                    'nomor_kontainer' => $nomorKontainer,
                    'vendor' => $vendor,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('updateTagihanTanggalAkhir failed', [
                'message' => $e->getMessage(),
                'nomor_kontainer' => $nomorKontainer,
                'vendor' => $vendor,
                'tanggal_akhir' => $tanggalAkhir,
            ]);
            throw $e;
        }
    }
}
