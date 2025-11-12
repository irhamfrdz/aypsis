<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PricelistUangJalanBatamTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return collection with example data
     */
    public function collection()
    {
        return collect([
            [
                'ATB',      // expedisi
                '1',        // ring
                '20FT',     // size (harus 20FT, 40FT, atau 45FT)
                'Full',     // f_e (Full atau Empty)
                170500,     // tarif (angka saja, tanpa format)
                'AQUA',     // status (AQUA, CHASIS PB, atau kosong)
            ],
            [
                'AYP',
                '2',
                '40FT',
                'Empty',
                150000,
                'CHASIS PB',
            ],
            [
                'ATB',
                '3',
                '45FT',
                'Full',
                200000,
                '',         // status kosong (optional)
            ],
        ]);
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'Expedisi',
            'Ring',
            'Size',
            'F/E',
            'Tarif',
            'Status',
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Expedisi
            'B' => 10,  // Ring
            'C' => 12,  // Size
            'D' => 12,  // F/E
            'E' => 15,  // Tarif
            'F' => 15,  // Status
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style data rows
        $sheet->getStyle('A2:F4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align for specific columns
        $sheet->getStyle('B2:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Add notes below the data
        $sheet->setCellValue('A6', 'CATATAN PENTING:');
        $sheet->setCellValue('A7', '1. Expedisi: Wajib diisi (contoh: ATB, AYP)');
        $sheet->setCellValue('A8', '2. Ring: Wajib diisi (contoh: 1, 2, 3)');
        $sheet->setCellValue('A9', '3. Size: Wajib diisi 20FT, 40FT, atau 45FT (bisa juga 20, 40, 45 saja akan otomatis ditambah FT)');
        $sheet->setCellValue('A10', '4. F/E: Wajib diisi Full atau Empty (tidak case-sensitive)');
        $sheet->setCellValue('A11', '5. Tarif: Wajib diisi, bisa format apa saja (170500 atau 170.500,00 atau 170,500.00)');
        $sheet->setCellValue('A12', '6. Status: Opsional, pilih AQUA atau CHASIS PB (tidak case-sensitive), atau kosongkan');
        $sheet->setCellValue('A13', '7. Hapus 3 baris contoh data sebelum import');
        $sheet->setCellValue('A14', '8. Data duplikat (expedisi+ring+size+f_e sama) akan otomatis diupdate');

        // Style notes
        $sheet->getStyle('A6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'DC2626'], // Red color
            ],
        ]);
        $sheet->getStyle('A7:A14')->applyFromArray([
            'font' => [
                'size' => 9,
                'color' => ['rgb' => '6B7280'], // Gray color
            ],
        ]);

        return [];
    }
}
