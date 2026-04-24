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
                170500,     // tarif_20ft_full
                150000,     // tarif_20ft_empty
                200000,     // tarif_40ft_full
                180000,     // tarif_40ft_empty
                50000,      // tarif_antar_lokasi
                'AQUA',     // status
            ],
            [
                'AYP',
                '2',
                160000,
                140000,
                190000,
                170000,
                0,
                'CHASIS PB',
            ],
            [
                'ATB',
                '3',
                180000,
                160000,
                210000,
                190000,
                60000,
                '',
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
            'Tarif 20FT Full',
            'Tarif 20FT Empty',
            'Tarif 40FT Full',
            'Tarif 40FT Empty',
            'Tarif Antar Lokasi',
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
            'C' => 18,  // Tarif 20FT Full
            'D' => 18,  // Tarif 20FT Empty
            'E' => 18,  // Tarif 40FT Full
            'F' => 18,  // Tarif 40FT Empty
            'G' => 20,  // Tarif Antar Lokasi
            'H' => 15,  // Status
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:H1')->applyFromArray([
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
        $sheet->getStyle('A2:H4')->applyFromArray([
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
        $sheet->getStyle('B2:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Add notes below the data
        $sheet->setCellValue('A6', 'CATATAN PENTING:');
        $sheet->setCellValue('A7', '1. Expedisi: Wajib diisi (contoh: ATB, AYP)');
        $sheet->setCellValue('A8', '2. Ring: Wajib diisi (contoh: 1, 2, 3)');
        $sheet->setCellValue('A9', '3. Tarif: Isi nominal sesuai kolom (20FT Full, 20FT Empty, dst)');
        $sheet->setCellValue('A10', '4. Tarif Antar Lokasi: Opsional, isi dengan nominal tambahan antar lokasi');
        $sheet->setCellValue('A11', '5. Status: Opsional, pilih AQUA atau CHASIS PB (tidak case-sensitive), atau kosongkan');
        $sheet->setCellValue('A12', '6. Hapus 3 baris contoh data sebelum import');
        $sheet->setCellValue('A13', '7. Data duplikat (expedisi+ring sama) akan otomatis diupdate');

        // Style notes
        $sheet->getStyle('A6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'DC2626'], // Red color
            ],
        ]);
        $sheet->getStyle('A7:A13')->applyFromArray([
            'font' => [
                'size' => 9,
                'color' => ['rgb' => '6B7280'], // Gray color
            ],
        ]);

        return [];
    }
}
