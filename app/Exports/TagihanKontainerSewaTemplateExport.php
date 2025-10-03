<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TagihanKontainerSewaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * Return template data with sample rows
     */
    public function array(): array
    {
        return [
            [
                'ZONA',
                'ZONA001234',
                '20',
                '2024-01-01',
                '2024-01-31',
                '25000',
                'GROUP001',
                'ongoing'
            ],
            [
                'DPE',
                'DPE567890',
                '40',
                '2024-01-01',
                '2024-01-31',
                '35000',
                'GROUP002',
                'selesai'
            ],
            [
                'ZONA',
                'ZONA002468',
                '20',
                '2024-02-01',
                '2024-02-29',
                '25000',
                '',
                'ongoing'
            ]
        ];
    }

    /**
     * Return headings for the template
     */
    public function headings(): array
    {
        return [
            'vendor',
            'nomor_kontainer',
            'size',
            'tanggal_awal',
            'tanggal_akhir',
            'tarif',
            'group',
            'status'
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ]
            ],
            // Style sample data rows
            2 => ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']]],
            3 => ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']]],
            4 => ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']]],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // vendor
            'B' => 20, // nomor_kontainer
            'C' => 10, // size
            'D' => 15, // tanggal_awal
            'E' => 15, // tanggal_akhir
            'F' => 15, // tarif
            'G' => 15, // group
            'H' => 12, // status
        ];
    }

    /**
     * Set worksheet title
     */
    public function title(): string
    {
        return 'Template Import';
    }
}
