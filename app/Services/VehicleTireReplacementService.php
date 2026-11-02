<?php

namespace App\Services;

use App\Models\StockBan;
use App\Models\StockBanLuarBatam;
use Illuminate\Support\Collection;

class VehicleTireReplacementService
{
    public function summarize(array $vehicleIds, string $start, string $end): Collection
    {
        if ($vehicleIds === []) {
            return collect();
        }

        $usages = collect();
        foreach ([StockBan::class, StockBanLuarBatam::class] as $model) {
            $rows = $model::query()
                ->whereIn('mobil_id', $vehicleIds)
                ->where('status', 'Terpakai')
                ->whereBetween('tanggal_keluar', [$start, $end])
                ->get(['id', 'mobil_id', 'nomor_bukti_pakai']);

            foreach ($rows as $row) {
                $proof = trim((string) $row->nomor_bukti_pakai);
                $usages->push([
                    'mobil_id' => $row->mobil_id,
                    // Keep proof numbers scoped to their stock source.
                    'event' => $model.'|'.($proof !== '' ? 'proof:'.$proof : 'stock:'.$row->id),
                ]);
            }
        }

        return $usages->groupBy('mobil_id')->map(fn (Collection $rows) => [
            'replacement_count' => $rows->unique('event')->count(),
            'tire_count' => $rows->count(),
        ]);
    }
}
