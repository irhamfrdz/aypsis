<?php

namespace App\Exports;

use App\Models\BiayaBensin;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BiayaBensinRangeExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private string $startDate, private string $endDate) {}

    public function query(): Builder
    {
        return BiayaBensin::query()
            ->with(['mobil', 'alatBerat', 'supir'])
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->orderBy('tanggal')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kendaraan / Alat Berat', 'No. Kartu', 'Supir', 'KM Awal', 'KM Akhir', 'Liter', 'Harga per Liter', 'Biaya', 'Status', 'Keterangan'];
    }

    public function map($item): array
    {
        $kendaraan = $item->mobil
            ? $item->mobil->nomor_polisi
            : ($item->alatBerat->nama ?? '-');

        return [
            $item->tanggal?->format('d/m/Y'),
            $kendaraan,
            $item->nomor_kartu ?: '-',
            $item->supir ? ($item->supir->nama_panggilan ?: $item->supir->nama_lengkap) : '-',
            $item->km_awal,
            $item->km_akhir,
            $item->liter,
            $item->harga_per_liter,
            $item->biaya,
            $item->status,
            $item->keterangan ?: '-',
        ];
    }
}
