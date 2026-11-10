<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VehicleTireReplacementService
{
    public function summarize(array $vehicleIds, string $start, string $end): Collection
    {
        if ($vehicleIds === []) {
            return collect();
        }

        $usages = DB::table('pemakaian_ban')
            ->whereNull('deleted_at')
            ->whereIn('jenis_komponen', ['ban', 'ban_dalam', 'ban_perut', 'ring_velg', 'velg'])
            ->whereNotNull('mobil_id')
            ->whereBetween('tanggal_digunakan', [$start, $end])
            ->whereIn('mobil_id', $vehicleIds)->get();

        $summary = collect();
        foreach ($vehicleIds as $vehicleId) {
            $matched = $usages->where('mobil_id', $vehicleId);
            if ($matched->isNotEmpty()) {
                $details = $matched->sortByDesc('tanggal_digunakan')->values()->map(fn ($row) => [
                    'tanggal' => $row->tanggal_digunakan,
                    'nomor_bukti' => $row->nomor_bukti_pakai,
                    'nomor_seri' => $row->nomor_seri,
                    'merk' => $row->merk,
                    'ukuran' => $row->ukuran,
                    'nama_barang' => $row->nama_barang,
                    'qty' => (int) $row->qty,
                    'kendaraan' => $row->unit_name ?: 'Unit #'.$row->mobil_id,
                    'penerima' => $row->penerima_name ?: '-',
                    'sumber' => match ($row->stock_table) {
                        'stock_bans' => 'Ban Luar',
                        'stock_ban_luar_batams' => 'Ban Luar Batam',
                        'stock_ring_velgs' => 'Ring Velg',
                        'stock_velgs' => 'Velg',
                        default => $row->nama_barang,
                    },
                    'keterangan' => $row->keterangan,
                ]);
                $summary->put($vehicleId, [
                    'replacement_count' => $matched->count(),
                    'tire_count' => (int) $matched->sum('qty'),
                    'details' => $details,
                ]);
            }
        }

        return $summary;
    }
}
