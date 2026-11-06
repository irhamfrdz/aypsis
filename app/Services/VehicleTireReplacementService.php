<?php

namespace App\Services;

use App\Models\StockBan;
use App\Models\StockBanLuarBatam;
use Illuminate\Support\Collection;

class VehicleTireReplacementService
{
    public function summarize(array $vehicleIds, string $start, string $end, array $drivers = []): Collection
    {
        if ($vehicleIds === []) {
            return collect();
        }

        $normalize = fn ($name) => mb_strtoupper(trim((string) $name));
        $driverIds = collect($drivers)->pluck('id')->filter()->unique()->all();
        $driverNames = collect($drivers)->flatMap(fn ($driver) => $driver['names'] ?? [])
            ->map($normalize)->filter()->unique()->values()->all();
        $usages = collect();
        foreach ([StockBan::class, StockBanLuarBatam::class] as $model) {
            $hasManualReceiver = $model === StockBan::class;
            $dateColumn = $hasManualReceiver ? 'COALESCE(tanggal_digunakan, tanggal_keluar)' : 'tanggal_keluar';
            $rows = $model::query()
                ->where('status', 'Terpakai')
                ->whereNotNull('mobil_id')
                ->whereRaw($dateColumn.' BETWEEN ? AND ?', [$start, $end])
                ->where(function ($query) use ($vehicleIds, $driverIds, $driverNames, $hasManualReceiver) {
                    $query->whereIn('mobil_id', $vehicleIds)->orWhereIn('penerima_id', $driverIds);
                    if ($hasManualReceiver && $driverNames !== []) {
                        $query->orWhereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(penerima_manual))'), $driverNames);
                    }
                })
                ->with(['mobil:id,nomor_polisi,no_kir,kode_no', 'penerima:id,nama_lengkap'])
                ->select(['id', 'mobil_id', 'penerima_id', 'nomor_bukti_pakai', 'nomor_seri', 'merk', 'ukuran', 'kondisi', 'harga_beli', 'keterangan'])
                ->selectRaw($hasManualReceiver ? 'penerima_manual' : 'NULL AS penerima_manual')
                ->selectRaw($dateColumn.' AS usage_date')
                ->get();
            $rows->each(fn ($row) => $row->setAttribute('stock_source', $hasManualReceiver ? 'Ban Luar' : 'Ban Luar Batam'));
            $usages = $usages->concat($rows);
        }

        $summary = collect();
        foreach ($vehicleIds as $vehicleId) {
            $driver = $drivers[$vehicleId] ?? null;
            $names = array_values(array_filter(array_map($normalize, $driver['names'] ?? [])));
            $matched = $usages->filter(function ($row) use ($driver, $names, $vehicleId, $normalize) {
                if ($driver && ($row->penerima_id || trim((string) $row->penerima_manual) !== '')) {
                    return $row->penerima_id
                        ? (string) $row->penerima_id === (string) $driver['id']
                        : in_array($normalize($row->penerima_manual), $names, true);
                }

                return (string) $row->mobil_id === (string) $vehicleId;
            });
            $count = $matched->count();
            if ($count > 0) {
                $details = $matched->sortByDesc('usage_date')->values()->map(fn ($row) => [
                    'tanggal' => $row->usage_date,
                    'nomor_bukti' => $row->nomor_bukti_pakai,
                    'nomor_seri' => $row->nomor_seri,
                    'merk' => $row->merk,
                    'ukuran' => $row->ukuran,
                    'kondisi' => $row->kondisi,
                    'harga_beli' => $row->harga_beli,
                    'kendaraan' => $row->mobil?->nomor_polisi ?: ($row->mobil?->no_kir ?: ($row->mobil?->kode_no ?: '-')),
                    'penerima' => $row->penerima?->nama_lengkap ?: ($row->penerima_manual ?: '-'),
                    'sumber' => $row->stock_source,
                    'keterangan' => $row->keterangan,
                ]);
                $summary->put($vehicleId, ['replacement_count' => $count, 'tire_count' => $count, 'details' => $details]);
            }
        }

        return $summary;
    }
}
