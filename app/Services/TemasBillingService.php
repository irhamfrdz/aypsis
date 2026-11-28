<?php

namespace App\Services;

use App\Models\BiayaKapal;
use App\Models\BiayaKapalTemas;
use App\Models\BiayaKapalTemasStage;
use App\Models\PricelistTemas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TemasBillingService
{
    private function cents($value): int
    {
        return (int) round((float) $value * 100);
    }

    private function defaultsToPerContainer(string $costName): bool
    {
        $normalized = strtoupper(trim(preg_replace('/[^A-Z0-9]+/i', ' ', $costName)));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        if (str_starts_with($normalized, 'BIAYA ')) {
            $normalized = trim(substr($normalized, 6));
        }
        if (in_array($normalized, ['ADM DO', 'DO KONTAINER', 'MATERAI'], true)) {
            return false;
        }

        return true;
    }

    private function temasQuantity(string $containerNumbers, bool $isPerContainer): int
    {
        if (! $isPerContainer) {
            return 1;
        }
        $containers = array_filter(array_unique(array_map(
            fn ($number) => strtoupper(trim($number)),
            explode(',', $containerNumbers)
        )));

        return max(1, count($containers));
    }

    public function balanceCents($dp, ?int $excludingInvoiceId = null, bool $lock = false): int
    {
        return app(SharedShippingDpService::class)->balanceCents($dp, $excludingInvoiceId, $lock);
    }

    public function candidates(?int $excludingInvoiceId = null)
    {
        return app(SharedShippingDpService::class)->candidates('temas', $excludingInvoiceId);
    }

    public function assertCanReplace(BiayaKapal $invoice): void
    {
        $stages = BiayaKapalTemasStage::where('biaya_kapal_id', $invoice->id)->orderBy('id')->lockForUpdate()->get();
        foreach ($stages as $stage) {
            // Even soft-deleted settlements keep the reference for audit and FK integrity.
            if ($stage->settlements()->lockForUpdate()->get()->isNotEmpty() || $stage->crossSettlements()->lockForUpdate()->get()->isNotEmpty()) {
                throw ValidationException::withMessages(['temas' => 'DP TEMAS sudah pernah digunakan untuk pelunasan. DP tidak dapat diubah atau dihapus.']);
            }
        }
    }

    /** Same entry workflow as Storage: DP first, determine the final invoice at settlement. */
    public function replace(BiayaKapal $invoice, array $sections): void
    {
        DB::transaction(function () use ($invoice, $sections) {
            $invoice = BiayaKapal::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            app(TemasPaymentService::class)->assertInvoiceEditable($invoice);
            $this->assertCanReplace($invoice);
            if (! $sections) {
                throw ValidationException::withMessages(['temas' => 'Tambahkan minimal satu bagian TEMAS.']);
            }
            app(SharedShippingDpService::class)->lockSources('temas', $invoice->id, $sections);
            BiayaKapalTemas::where('biaya_kapal_id', $invoice->id)->delete();
            BiayaKapalTemasStage::where('biaya_kapal_id', $invoice->id)->delete();
            foreach ($sections as $index => $section) {
                $this->storeSection($invoice, $section, $index);
            }
            $stages = BiayaKapalTemasStage::where('biaya_kapal_id', $invoice->id)->get();
            $nominal = $stages->sum('nominal_dibayar');
            $invoice->update([
                'nominal' => $nominal, 'total_biaya' => $nominal,
                'nama_kapal' => $stages->pluck('kapal')->unique()->values()->all(),
                'no_voyage' => $stages->pluck('voyage')->unique()->values()->all(),
                'status_pembayaran' => $nominal == 0 && $stages->contains('payment_mode', 'pelunasan_dp') ? 'paid' : 'pending',
                'sisa_pembayaran' => $nominal,
            ]);
        });
    }

    private function storeSection(BiayaKapal $invoice, array $section, $index): void
    {
        $mode = $section['payment_mode'] ?? 'lunas';
        if (! isset($section['dp_stage_ids']) && ! empty($section['dp_stage_id'])) {
            $section['dp_stage_ids'] = [$section['dp_stage_id']];
        }
        Validator::make($section, [
            'payment_mode' => 'sometimes|in:lunas,dp,pelunasan_dp',
            'nominal_dibayar' => 'required_if:payment_mode,dp|nullable|numeric|decimal:0,2|gt:0',
            'tanggal_dp' => 'nullable|required_if:payment_mode,dp|date',
            'nama_bank' => 'nullable|required_if:payment_mode,dp|string|max:255',
            'keterangan_dp' => 'nullable|string|max:5000',
            'dp_stage_ids' => 'required_if:payment_mode,pelunasan_dp|nullable|array|min:1',
            'dp_stage_ids.*' => 'required|distinct',
        ])->validate();
        $dps = collect();
        $balances = [];
        if ($mode === 'pelunasan_dp') {
            $dps = app(SharedShippingDpService::class)->resolve($section['dp_stage_ids'], 'temas', $index);
            foreach ($dps as $dp) {
                if ($dp->payment_mode !== 'dp' || $dp->nominal_dibayar <= 0 || $dp->biaya_kapal_id == $invoice->id) {
                    throw ValidationException::withMessages(["temas.$index.dp_stage_ids" => 'Referensi harus berupa DP aktif dari invoice lain.']);
                }
                $balances[app(SharedShippingDpService::class)->key($dp)] = $this->balanceCents($dp, null, true);
                $dpDate = $dp->tanggal_dp ?? $dp->biayaKapal->tanggal;
                if ($invoice->tanggal && $dpDate && $invoice->tanggal->lt($dpDate)) {
                    throw ValidationException::withMessages(['tanggal' => 'Tanggal pelunasan tidak boleh mendahului tanggal DP.']);
                }
            }
            if (array_sum($balances) <= 0) {
                throw ValidationException::withMessages(["temas.$index.dp_stage_ids" => 'Saldo DP sudah habis. Pilih referensi DP lain atau pembayaran langsung.']);
            }
            $dps = $dps->sortBy(fn ($dp) => ($dp->tanggal_dp?->format('Y-m-d') ?? $dp->biayaKapal->tanggal?->format('Y-m-d') ?? '9999-12-31').app(SharedShippingDpService::class)->source($dp).sprintf('%020d', $dp->id))->values();
        }
        $advance = 0;
        $allocations = [];
        Validator::make($section, ['kapal' => 'required|string|max:255', 'voyage' => 'required|string|max:255'])->validate();
        $common = array_intersect_key($section, array_flip([
            'kapal', 'voyage', 'penerima', 'nomor_rekening', 'nomor_referensi', 'tanggal_invoice_vendor', 'keterangan',
        ]));
        $common['tanggal_invoice_vendor'] = $common['tanggal_invoice_vendor'] ?? null;
        $common['biaya_kapal_id'] = $invoice->id;
        $common['pph_reimburse'] = $mode === 'lunas' && filter_var($section['pph_reimburse'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $rows = [];
        if ($mode === 'dp') {
            $cash = $this->cents($section['nominal_dibayar']);
            $total = 0;
            $rows[] = array_merge($common, [
                'jenis_biaya' => 'DP / Uang Muka TEMAS', 'kuantitas' => 1,
                'harga' => $cash / 100, 'sub_total' => $cash / 100, 'grand_total' => $cash / 100,
                'pph_active' => false, 'ppn_active' => false,
            ]);
        } else {
            Validator::make($section, [
                'types' => 'required|array|min:1', 'types.*' => 'required',
                'custom_prices' => 'required|array', 'custom_prices.*' => 'required|numeric|min:0',
                'quantities' => 'required|array', 'quantities.*' => 'required|numeric|gt:0',
                'nomor_kontainers' => 'required|array', 'nomor_kontainers.*' => 'required|string',
                'nomor_bls' => 'required|array', 'nomor_bls.*' => 'required|string|max:255',
                'size_items' => 'required|array', 'size_items.*' => 'required|in:20ft,40ft,45ft',
                'per_containers' => 'sometimes|array', 'per_containers.*' => 'boolean',
            ])->validate();
            foreach ($section['types'] as $i => $type) {
                foreach (['custom_prices', 'quantities', 'nomor_kontainers', 'nomor_bls', 'size_items'] as $field) {
                    if (! array_key_exists($i, $section[$field])) {
                        throw ValidationException::withMessages(["temas.$index.$field" => 'Rincian nomor BL, kontainer, dan biaya tidak lengkap.']);
                    }
                }
                $master = $type === 'MANUAL' ? null : PricelistTemas::find($type);
                $label = $master?->jenis_biaya ?? trim($section['manual_names'][$i] ?? '');
                if (($type !== 'MANUAL' && ! $master) || $label === '') {
                    throw ValidationException::withMessages(["temas.$index.types" => 'Pilih jenis biaya yang valid atau isi nama biaya manual.']);
                }
                $number = strtoupper(trim($section['nomor_kontainers'][$i]));
                if ($number === '') {
                    throw ValidationException::withMessages(["temas.$index.nomor_kontainers" => 'Nomor kontainer wajib diisi.']);
                }
                $isPerContainer = array_key_exists($i, $section['per_containers'] ?? [])
                    ? (bool) $section['per_containers'][$i]
                    : $this->defaultsToPerContainer($label);
                $quantity = $this->temasQuantity($number, $isPerContainer);
                $sub = $this->cents((float) $section['custom_prices'][$i] * $quantity);
                $nomorBl = trim($section['nomor_bls'][$i]);
                $nomorBl = preg_replace('/-\d+$/', '', $nomorBl) ?: $nomorBl;
                $rows[] = array_merge($common, [
                    'nomor_kontainer' => $number, 'nomor_bl' => $nomorBl,
                    'bl_id' => ($section['bl_ids'][$i] ?? null) ?: null,
                    'pricelist_temas_id' => $master?->id, 'jenis_biaya' => $label,
                    'lokasi' => $section['lokasi_items'][$i] ?? null, 'size' => $section['size_items'][$i],
                    'harga' => $section['custom_prices'][$i], 'kuantitas' => $quantity,
                    'sub_total' => $sub / 100, 'grand_total' => $sub / 100,
                    'is_muat' => ($section['is_muat'][$i] ?? 0) == 1,
                    'is_bongkar' => ($section['is_bongkar'][$i] ?? 0) == 1,
                    'is_per_container' => $isPerContainer,
                    'pph_active' => false, 'ppn_active' => false,
                ]);
            }
            $subTotal = array_sum(array_map(fn ($row) => $this->cents($row['sub_total']), $rows));
            // Like Storage, DP settlement is final container cost minus the recorded advance.
            $extras = 0;
            if ($mode === 'lunas') {
                foreach (['pph', 'ppn', 'biaya_materai', 'biaya_admin', 'adjustment'] as $field) {
                    $amount = $this->cents($section[$field] ?? 0);
                    $rows[0][$field] = $amount / 100;
                    if ($field === 'pph' || $field === 'ppn') {
                        $rows[0][$field.'_active'] = isset($section[$field.'_active']);
                        if ($rows[0][$field.'_active']) {
                            $extras += $field === 'pph' ? -$amount : $amount;
                        }
                    } else {
                        $extras += $amount;
                    }
                }
            }
            $total = $subTotal + $extras;
            if ($total < 0 || ($mode === 'pelunasan_dp' && $total <= 0)) {
                throw ValidationException::withMessages(["temas.$index.types" => 'Tagihan pelunasan harus lebih dari nol.']);
            }
            foreach ($dps as $dp) {
                $used = min($balances[app(SharedShippingDpService::class)->key($dp)], $total - $advance);
                if ($used > 0) {
                    $allocations[app(SharedShippingDpService::class)->key($dp)] = ['nominal_digunakan' => $used / 100];
                    $advance += $used;
                }
            }
            $cash = $total - $advance;
            // Allocate the cash amount once across cost rows, including cent rounding remainder.
            $allocated = 0;
            $cumulative = 0;
            foreach ($rows as &$row) {
                $cumulative += $this->cents($row['sub_total']);
                $target = $subTotal > 0 ? (int) round($cash * ($cumulative / $subTotal)) : $cash;
                $row['grand_total'] = ($target - $allocated) / 100;
                $allocated = $target;
            }
            unset($row);
        }
        $stage = BiayaKapalTemasStage::create([
            'biaya_kapal_id' => $invoice->id, 'kapal' => $section['kapal'], 'voyage' => $section['voyage'],
            'payment_mode' => $mode, 'dp_stage_id' => null,
            'tanggal_dp' => $mode === 'dp' ? ($section['tanggal_dp'] ?? null) : null,
            'nama_bank' => $mode === 'dp' ? ($section['nama_bank'] ?? null) : null,
            'keterangan_dp' => $mode === 'dp' ? ($section['keterangan_dp'] ?? null) : null,
            'nilai_tagihan' => $total / 100, 'nominal_dibayar' => $cash / 100,
            'dp_diperhitungkan' => $advance / 100,
        ]);
        app(SharedShippingDpService::class)->sync($stage, $allocations);
        foreach ($rows as $row) {
            $stage->details()->create($row);
        }
    }
}
