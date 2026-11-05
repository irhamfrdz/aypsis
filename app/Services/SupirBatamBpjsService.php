<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use App\Models\PranotaBpjsDetail;
use Illuminate\Support\Collection;

class SupirBatamBpjsService
{
    public function latestForEmployees(Collection $employees): array
    {
        $employees = $employees->filter()->unique('id');
        if ($employees->isEmpty()) {
            return [];
        }
        $niks = $employees->pluck('nik')->filter()->values()->all();
        $details = PranotaBpjsDetail::with(['karyawan', 'header'])
            ->join('pranota_bpjs_headers as headers', 'headers.id', '=', 'pranota_bpjs_details.pranota_bpjs_header_id')
            ->whereNull('headers.deleted_at')
            ->where(function ($query) use ($niks) {
                $query->whereIn('snapshot_bpjs->karyawan->nik', $niks)
                    ->orWhereHasMorph('karyawan', [Karyawan::class, KaryawanTidakTetap::class], fn ($employee) => $employee->whereIn('nik', $niks));
            })
            ->orderByDesc('headers.tanggal_pranota')->orderByDesc('headers.id')
            ->orderByDesc('pranota_bpjs_details.id')
            ->select('pranota_bpjs_details.*')->get();

        $byIdentity = [];
        foreach ($details as $detail) {
            $employee = $detail->karyawan_pranota;
            $key = $this->identity($employee?->nik, $employee?->nama_lengkap);
            if ($key === null || isset($byIdentity[$key])) {
                continue;
            }
            // Only this component is JHT 2% (Hutang); the legacy column combines other debts.
            $byIdentity[$key] = [
                'amount' => round((float) ($detail->rincian_bpjs['noncrew_jht_hutang'] ?? 0), 2),
                'detail_id' => $detail->id,
                'number' => $detail->header->nomor_pranota,
            ];
        }

        return $employees->mapWithKeys(fn ($employee) => [
            $employee->id => $byIdentity[$this->identity($employee->nik, $employee->nama_lengkap) ?? '']
                ?? ['amount' => 0, 'detail_id' => null, 'number' => null],
        ])->all();
    }

    private function identity(?string $nik, ?string $name): ?string
    {
        $nik = trim((string) $nik);
        $name = mb_strtoupper(preg_replace('/\s+/u', ' ', trim((string) $name)));

        return $nik !== '' && $name !== '' ? json_encode([$nik, $name]) : null;
    }
}
