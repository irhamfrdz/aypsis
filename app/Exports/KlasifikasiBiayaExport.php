<?php

namespace App\Exports;

use App\Models\KlasifikasiBiaya;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KlasifikasiBiayaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return KlasifikasiBiaya::orderBy('kode', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama',
            'Deskripsi',
            'Status',
        ];
    }

    public function map($klasifikasi): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $klasifikasi->kode,
            $klasifikasi->nama,
            $klasifikasi->deskripsi,
            $klasifikasi->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }
}
