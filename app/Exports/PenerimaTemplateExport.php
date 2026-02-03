<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class PenerimaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'Contoh Penerima', 
                'Jl. Contoh No. 1', 
                '12.345.678.9-000.000', 
                '1234567890123456', 
                'Catatan contoh', 
                'active'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'nama_penerima', 
            'alamat', 
            'npwp', 
            'nitku', 
            'catatan', 
            'status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], 
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // nama_penerima
            'B' => 50, // alamat
            'C' => 20, // npwp
            'D' => 20, // nitku
            'E' => 30, // catatan
            'F' => 15, // status
        ];
    }
}
