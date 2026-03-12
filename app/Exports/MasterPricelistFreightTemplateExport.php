<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterPricelistFreightTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Freight 20 FT',
                'Jakarta',
                'Vendor A',
                '5000000',
                'Aktif',
                'Contoh data'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Lokasi',
            'Vendor',
            'Tarif',
            'Status',
            'Keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
