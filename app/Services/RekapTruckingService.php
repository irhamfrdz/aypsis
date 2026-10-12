<?php

namespace App\Services;

use App\Models\BiayaKapalTrucking;
use Illuminate\Support\Collection;

class RekapTruckingService
{
    public function rows(BiayaKapalTrucking $detail, Collection $manifests): Collection
    {
        $catalog = $manifests->keyBy('id');
        $adjustments = $detail->container_adjustments ?? [];
        $rows = collect($detail->no_bl ?? [])->unique()->values()->map(function ($id) use ($catalog, $adjustments, $detail) {
            $manifest = $catalog->get($id);
            $cargo = strtoupper(trim((string) $manifest?->tipe_kontainer)) === 'CARGO';
            $number = trim((string) $manifest?->nomor_kontainer);
            $size = preg_replace('/\D/', '', (string) $manifest?->size_kontainer);

            return [
                'label' => $cargo ? 'Cargo'.($manifest?->nama_barang ? ' - '.$manifest->nama_barang : '')
                    : ($number ?: 'Tanpa nomor kontainer (manifest '.$id.')'),
                'jenis' => $cargo ? 'cargo' : ($number ? 'kontainer' : 'lainnya'),
                'bl' => $manifest?->nomor_bl ?: '-', 'size' => $size ?: '-',
                'vendor' => $detail->nama_vendor ?: '-',
                'container_adjustment' => (float) ($adjustments[$id] ?? 0),
            ];
        });
        if ($rows->isEmpty()) {
            $rows->push(['label' => 'Tanpa rincian kontainer', 'jenis' => 'lainnya', 'bl' => '-',
                'size' => '-', 'vendor' => $detail->nama_vendor ?: '-', 'container_adjustment' => 0]);
        }

        // Allocate stored size totals, without recalculating old invoices from today's pricelist.
        // Container adjustments are already included in those totals and must not be added twice.
        $budgets = [
            '20' => (float) $detail->total_biaya_20ft,
            '40' => (float) $detail->total_biaya_40ft,
            '-' => max(0, (float) $detail->subtotal - (float) $detail->total_biaya_20ft - (float) $detail->total_biaya_40ft),
        ];
        $weights = array_fill(0, $rows->count(), 0);
        foreach ($rows->groupBy(fn ($row) => in_array($row['size'], ['20', '40']) ? $row['size'] : '-', true) as $size => $group) {
            $budget = $budgets[$size] ?? 0;
            $base = ($budget - $group->sum('container_adjustment')) / $group->count();
            $groupWeights = [];
            foreach ($group as $index => $row) {
                $groupWeights[$index] = max(0, $base + $row['container_adjustment']);
            }
            $groupTotal = array_sum($groupWeights);
            foreach ($groupWeights as $index => $weight) {
                $weights[$index] = $groupTotal > 0 ? $weight * $budget / $groupTotal : 0;
            }
        }
        if (((float) $detail->total_biaya_20ft + (float) $detail->total_biaya_40ft) <= 0 || array_sum($weights) <= 0) {
            $weights = array_fill(0, $rows->count(), 1);
        }
        foreach (['subtotal', 'adjustment', 'pph', 'total_biaya'] as $field) {
            $shares = $this->allocate((float) $detail->{$field}, $weights);
            $rows = $rows->map(fn ($row, $index) => $row + [($field === 'total_biaya' ? 'total' : $field) => $shares[$index]]);
        }

        return $rows;
    }

    private function allocate(float $amount, array $weights): array
    {
        $cents = (int) round(abs($amount) * 100);
        $totalWeight = array_sum($weights);
        $shares = $remainders = [];
        foreach ($weights as $index => $weight) {
            $raw = $cents * $weight / $totalWeight;
            $shares[$index] = (int) floor($raw);
            $remainders[$index] = $raw - $shares[$index];
        }
        $remaining = $cents - array_sum($shares);
        arsort($remainders);
        foreach (array_keys($remainders) as $index) {
            if ($remaining-- <= 0) {
                break;
            }
            $shares[$index]++;
        }

        return array_map(fn ($share) => ($amount < 0 ? -1 : 1) * $share / 100, $shares);
    }
}
