<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UangMakanTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['1234', '2026-06-27', 50000, 'Uang makan harian'],
            ['5678', '2026-06-27', 50000, 'Uang makan lembur'],
        ];
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Tanggal',
            'Nominal',
            'Keterangan'
        ];
    }
}
