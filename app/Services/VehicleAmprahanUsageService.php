<?php

namespace App\Services;

use App\Models\StockAmprahanUsage;
use Illuminate\Support\Collection;

class VehicleAmprahanUsageService
{
    public function forVehicles(array $vehicleIds, string $fromDate, string $toDate): Collection
    {
        $vehicleIds = array_values(array_unique(array_filter($vehicleIds)));
        if ($vehicleIds === []) {
            return collect();
        }

        $usages = StockAmprahanUsage::query()
            ->with('stockAmprahan.masterNamaBarangAmprahan')
            ->whereBetween('tanggal_pengambilan', [$fromDate, $toDate])
            ->where(function ($query) use ($vehicleIds) {
                $query->whereIn('kendaraan_id', $vehicleIds)
                    ->orWhereIn('truck_id', $vehicleIds)
                    ->orWhereIn('buntut_id', $vehicleIds);
            })
            ->orderByDesc('tanggal_pengambilan')
            ->orderByDesc('id')
            ->get();

        $selectedIds = array_fill_keys($vehicleIds, true);
        $byVehicle = collect();

        foreach ($usages as $usage) {
            $stock = $usage->stockAmprahan;
            $detail = [
                'tanggal' => $usage->tanggal_pengambilan,
                'nama_barang' => $stock?->nama_barang ?: ($stock?->masterNamaBarangAmprahan?->nama_barang ?: '-'),
                'type_barang' => $stock?->type_barang,
                'jumlah' => $usage->jumlah,
                'satuan' => $stock?->satuan,
                'keterangan' => $usage->keterangan,
                'nomor_bukti' => $stock?->nomor_bukti,
            ];

            $rolesByVehicle = [];
            foreach (['kendaraan_id' => 'Kendaraan', 'truck_id' => 'Truck', 'buntut_id' => 'Buntut'] as $column => $role) {
                $vehicleId = $usage->{$column};
                if ($vehicleId && isset($selectedIds[$vehicleId])) {
                    $rolesByVehicle[$vehicleId][] = $role;
                }
            }

            foreach ($rolesByVehicle as $vehicleId => $roles) {
                $byVehicle[$vehicleId] ??= collect();
                $byVehicle[$vehicleId]->push($detail + ['posisi' => implode(' / ', $roles)]);
            }
        }

        return $byVehicle;
    }
}
