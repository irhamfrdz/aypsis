<?php

namespace App\Services;

use App\Models\TagihanOb;
use Illuminate\Support\Collection;

class VehicleObCountService
{
    public function forVehicles(Collection $vehicles, string $from, string $to): Collection
    {
        $normalize = fn ($plate) => preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $plate));
        $vehicles = $vehicles->filter()->unique('id');
        $counts = $vehicles->mapWithKeys(fn ($vehicle) => [$vehicle->id => 0]);
        $byPlate = $vehicles->filter(fn ($vehicle) => $normalize($vehicle->nomor_polisi) !== '')
            ->groupBy(fn ($vehicle) => $normalize($vehicle->nomor_polisi));
        // One invoice is one OB, including invoices containing two containers.
        $groups = TagihanOb::query()->whereDate('tanggal_ob', '>=', $from)->whereDate('tanggal_ob', '<=', $to)
            ->select('mobil_id', 'nomor_polisi')->selectRaw('COUNT(*) as ob_count')
            ->groupBy('mobil_id', 'nomor_polisi')->get();
        foreach ($groups as $group) {
            $id = $group->mobil_id;
            if ($id === null) {
                $candidates = $byPlate->get($normalize($group->nomor_polisi), collect());
                if ($candidates->count() !== 1) {
                    continue;
                }
                $id = $candidates->first()->id;
            }
            // A recorded mobil_id takes precedence, even when the plate differs.
            if ($counts->has($id)) {
                $counts->put($id, $counts->get($id) + (int) $group->ob_count);
            }
        }

        return $counts;
    }
}
