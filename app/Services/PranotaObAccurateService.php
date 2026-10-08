<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PranotaObAccurateService
{
    public function attach(Collection $items, $from, $until): Collection
    {
        if ($items->isEmpty()) {
            return $items;
        }
        $sourceRows = DB::table('pranota_ob_items')->join('pranota_obs', 'pranota_obs.id', '=', 'pranota_ob_items.pranota_ob_id')
            ->whereBetween('pranota_obs.tanggal_ob', [$from, $until])
            ->select('pranota_obs.id', 'pranota_obs.tanggal_ob', 'pranota_obs.no_voyage', 'pranota_ob_items.supir')->get();
        $idsByRow = $sourceRows->groupBy(fn ($row) => $this->key($row))->map(fn ($rows) => $rows->pluck('id')->unique()->all());
        $reportIds = $sourceRows->pluck('id')->unique()->all();
        $payments = DB::table('pembayaran_pranota_obs')->get()->filter(fn ($payment) =>
            ! in_array(strtolower($payment->status ?? ''), ['cancelled', 'dibatalkan', 'rejected'])
            && count(array_intersect($reportIds, $this->ids($payment->pranota_ob_ids))) > 0);
        $referencedDpIds = $payments->flatMap(fn ($payment) => $this->ids($payment->pembayaran_ob_ids ?? null, $payment->pembayaran_ob_id ?? null))->unique()->all();
        $dps = DB::table('pembayaran_obs')->where(function ($query) use ($items, $referencedDpIds) {
            $query->whereIn('nomor_voyage', $items->pluck('no_voyage')->filter()->unique()->all());
            if ($referencedDpIds) {
                $query->orWhereIn('id', $referencedDpIds);
            }
        })->get()->filter(fn ($dp) => ! in_array(strtolower($dp->status ?? ''), ['cancelled', 'dibatalkan', 'rejected']));
        $drivers = DB::table('karyawans')->get(['id', 'nik', 'nama_panggilan', 'nama_lengkap']);

        return $items->map(function ($item) use ($idsByRow, $payments, $dps, $drivers) {
            $pranotaIds = isset($item->pranota_id) ? [$item->pranota_id] : ($idsByRow[$this->key($item)] ?? []);
            [$dpNumbers, $paymentNumbers] = $this->numbersForRow($item, $pranotaIds, $payments, $dps, $drivers);
            $item->accurate_dp = $dpNumbers;
            $item->accurate_pelunasan = $paymentNumbers;

            return $item;
        });
    }

    private function key($row): string
    {
        return json_encode([substr((string) $row->tanggal_ob, 0, 10), (string) $row->no_voyage, (string) $row->supir]);
    }

    private function ids($value, $fallback = null): array
    {
        for ($attempt = 0; $attempt < 2 && is_string($value); $attempt++) {
            $value = json_decode($value, true);
        }
        $ids = is_array($value) ? $value : [];

        return array_values(array_unique(array_map('intval', $ids ?: ($fallback ? [$fallback] : []))));
    }

    public function numbersForRow($item, array $pranotaIds, Collection $payments, Collection $dps, Collection $drivers): array
    {
        $matchingPayments = $payments->filter(fn ($payment) => count(array_intersect($pranotaIds, $this->ids($payment->pranota_ob_ids))) > 0);
        $linkedIds = $matchingPayments->flatMap(fn ($payment) => $this->ids($payment->pembayaran_ob_ids ?? null, $payment->pembayaran_ob_id ?? null))->unique();
        $driverIds = $drivers->filter(fn ($driver) => ! empty($item->nik) ? (string) $driver->nik === (string) $item->nik
            : in_array(strtolower(trim($item->supir ?? '')), array_filter([
                strtolower(trim($driver->nama_panggilan ?? '')), strtolower(trim($driver->nama_lengkap ?? '')),
            ])))->pluck('id')->all();
        $matchingDps = $dps->filter(function ($dp) use ($item, $linkedIds, $driverIds, $matchingPayments) {
            $belongs = $matchingPayments->isNotEmpty() ? $linkedIds->contains((int) $dp->id)
                : trim($item->no_voyage ?? '') !== '' && strtoupper(trim($dp->nomor_voyage ?? '')) === strtoupper(trim($item->no_voyage ?? ''));

            return $belongs && count(array_intersect($driverIds, $this->ids($dp->supir_ids))) > 0;
        });
        $numbers = fn ($rows) => $rows->pluck('nomor_accurate')->map(fn ($number) => trim((string) $number))->filter()->unique()->values()->all();

        return [$numbers($matchingDps), $numbers($matchingPayments)];
    }
}
