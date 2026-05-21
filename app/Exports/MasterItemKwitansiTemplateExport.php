<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterItemKwitansiTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'kode',
            'nama_item',
            'group',
            'keterangan',
        ];
    }

    public function array(): array
    {
        return [
            [
                'ITEM001',
                'Biaya Handling Kontainer 20ft',
                'HANDLING',
                'Contoh keterangan item kwitansi',
            ],
            [
                'ITEM002',
                'Biaya Administrasi',
                'ADMIN',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
