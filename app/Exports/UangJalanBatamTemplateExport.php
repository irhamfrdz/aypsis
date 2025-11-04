<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UangJalanBatamTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    public function array(): array
    {
        // Return sample data for template
        return [
            [
                'Batam',
                'Jakarta - Batam',
                'JNE',
                'Ring 1',
                '20FT',
                'Full',
                '500000',
                'aqua',
                '2025-01-01',
                '2025-12-31'
            ],
            [
                'Batam',
                'Surabaya - Batam',
                'TIKI',
                'Ring 2',
                '40FT',
                'Empty',
                '750000',
                'chasis PB',
                '2025-01-01',
                '2025-12-31'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Wilayah',
            'Rute',
            'Expedisi',
            'Ring',
            'FT',
            'F/E',
            'Tarif',
            'Status',
            'Tanggal Awal Berlaku',
            'Tanggal Akhir Berlaku'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 10,
            'E' => 8,
            'F' => 8,
            'G' => 15,
            'H' => 12,
            'I' => 20,
            'J' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Data rows
            '2:100' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
