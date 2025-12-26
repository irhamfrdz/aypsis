<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class KlasifikasiBiayaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function array(): array
    {
        return [
            ['KB00001', 'Biaya Bongkar', 'Biaya bongkar muatan', 'active'],
            ['', 'Biaya Tambahan', 'Deskripsi opsional', 'inactive']
        ];
    }

    public function headings(): array
    {
        return ['kode', 'nama', 'deskripsi', 'is_active'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Heading style
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 40,
            'C' => 60,
            'D' => 15,
        ];
    }

    public function title(): string
    {
        return 'Template Klasifikasi Biaya';
    }
}
