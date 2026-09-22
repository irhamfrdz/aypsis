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

    public function candidates()
    {
        return BiayaKapalTemasStage::where('payment_mode', 'dp')
            ->where('nominal_dibayar', '>', 0)
            ->whereHas('biayaKapal', fn ($q) => $q->where('status_pembayaran', '!=', 'cancelled'))
            ->whereDoesntHave('settlements', fn ($q) => $q->whereHas('biayaKapal'));
    }

    public function assertCanReplace(BiayaKapal $invoice): void
    {
        $stages = BiayaKapalTemasStage::where('biaya_kapal_id', $invoice->id)->orderBy('id')->lockForUpdate()->get();
        foreach ($stages as $stage) {
            // Even soft-deleted settlements keep the reference for audit and FK integrity.
            if ($stage->settlements()->lockForUpdate()->get()->isNotEmpty()) {
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
            ]);
        });
    }

    private function storeSection(BiayaKapal $invoice, array $section, $index): void
    {
        $mode = $section['payment_mode'] ?? 'lunas';
        Validator::make($section, [
            'payment_mode' => 'sometimes|in:lunas,dp,pelunasan_dp',
            'nominal_dibayar' => 'required_if:payment_mode,dp|nullable|numeric|decimal:0,2|gt:0',
            'dp_stage_id' => 'required_if:payment_mode,pelunasan_dp|nullable|integer',
        ])->validate();
        $dp = null;
        if ($mode === 'pelunasan_dp') {
            $dp = BiayaKapalTemasStage::query()
                ->join('biaya_kapals as dp_invoice', 'dp_invoice.id', '=', 'biaya_kapal_temas_stages.biaya_kapal_id')
                ->where('biaya_kapal_temas_stages.id', $section['dp_stage_id'])
                ->whereNull('dp_invoice.deleted_at')->where('dp_invoice.status_pembayaran', '!=', 'cancelled')
                ->select('biaya_kapal_temas_stages.*')->lockForUpdate()->first();
            // Locking reads see the latest committed state even under MySQL REPEATABLE READ.
            $settled = $dp && DB::table('biaya_kapal_temas_stages as stages')
                ->join('biaya_kapals as invoices', 'invoices.id', '=', 'stages.biaya_kapal_id')
                ->where('stages.dp_stage_id', $dp->id)->whereNull('invoices.deleted_at')
                ->select('stages.id')->lockForUpdate()->get()->isNotEmpty();
            if (! $dp || $dp->payment_mode !== 'dp' || $dp->nominal_dibayar <= 0 || $settled || $dp->biaya_kapal_id == $invoice->id) {
                throw ValidationException::withMessages(["temas.$index.dp_stage_id" => 'DP tidak tersedia, sudah dilunasi, atau berasal dari invoice yang sama.']);
            }
            $section['kapal'] = $dp->kapal;
            $section['voyage'] = $dp->voyage;
            if ($invoice->tanggal && $dp->biayaKapal->tanggal && $invoice->tanggal->lt($dp->biayaKapal->tanggal)) {
                throw ValidationException::withMessages(['tanggal' => 'Tanggal pelunasan tidak boleh mendahului tanggal DP.']);
            }
        }
        Validator::make($section, ['kapal' => 'required|string|max:255', 'voyage' => 'required|string|max:255'])->validate();
        $common = array_intersect_key($section, array_flip([
            'kapal', 'voyage', 'penerima', 'nomor_rekening', 'nomor_referensi', 'tanggal_invoice_vendor', 'keterangan',
        ]));
        $common['tanggal_invoice_vendor'] = $common['tanggal_invoice_vendor'] ?? null;
        $common['biaya_kapal_id'] = $invoice->id;
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
                'nomor_kontainers' => 'required|array', 'nomor_kontainers.*' => 'required|string|max:100',
                'size_items' => 'required|array', 'size_items.*' => 'required|in:20ft,40ft,45ft',
            ])->validate();
            foreach ($section['types'] as $i => $type) {
                foreach (['custom_prices', 'quantities', 'nomor_kontainers', 'size_items'] as $field) {
                    if (! array_key_exists($i, $section[$field])) {
                        throw ValidationException::withMessages(["temas.$index.$field" => 'Rincian kontainer dan biaya tidak lengkap.']);
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
                $sub = $this->cents((float) $section['custom_prices'][$i] * (float) $section['quantities'][$i]);
                $rows[] = array_merge($common, [
                    'nomor_kontainer' => $number, 'bl_id' => ($section['bl_ids'][$i] ?? null) ?: null,
                    'pricelist_temas_id' => $master?->id, 'jenis_biaya' => $label,
                    'lokasi' => $section['lokasi_items'][$i] ?? null, 'size' => $section['size_items'][$i],
                    'harga' => $section['custom_prices'][$i], 'kuantitas' => $section['quantities'][$i],
                    'sub_total' => $sub / 100, 'grand_total' => $sub / 100,
                    'is_muat' => ($section['is_muat'][$i] ?? 0) == 1,
                    'is_bongkar' => ($section['is_bongkar'][$i] ?? 0) == 1,
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
            $advance = $dp ? $this->cents($dp->nominal_dibayar) : 0;
            if ($total < $advance || $total < 0) {
                throw ValidationException::withMessages(["temas.$index.types" => 'Tagihan akhir tidak boleh lebih kecil dari DP. Periksa biaya kontainer.']);
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
            'payment_mode' => $mode, 'dp_stage_id' => $dp?->id,
            'nilai_tagihan' => $total / 100, 'nominal_dibayar' => $cash / 100,
            'dp_diperhitungkan' => $dp?->nominal_dibayar ?? 0,
        ]);
        foreach ($rows as $row) {
            $stage->details()->create($row);
        }
    }
}
