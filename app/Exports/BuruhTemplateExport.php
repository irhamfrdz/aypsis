<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BuruhTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'nama',
            'nik',
            'status',
            'status_bpjs',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Budi Santoso',
                '1234567890',
                'aktif',
                'aktif',
            ],
            [
                'Siti Aminah',
                '0987654321',
                'non-aktif',
                'tidak aktif',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
