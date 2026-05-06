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
            'alamat',
            'status',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Budi Santoso',
                '1234567890',
                'Jl. Contoh No. 123',
                'aktif',
            ],
            [
                'Siti Aminah',
                '0987654321',
                'Jl. Sampel No. 456',
                'non-aktif',
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
