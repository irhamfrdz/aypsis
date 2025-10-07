<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KontainerTemplateExport
{
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set sheet title
        $sheet->setTitle('Template Kontainer');

        // Headers
        $headers = [
            'A1' => 'Nomor Seri Gabungan',
            'B1' => 'Ukuran',
            'C1' => 'Tipe Kontainer',
            'D1' => 'Status'
        ];

        // Set headers
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['CBHU1234567', '20', 'DRY', 'Tersedia'],
            ['CCLU7654321', '40', 'DRY', 'Disewa'],
            ['TEMU9876543', '20', 'REEFER', 'Tersedia'],
        ];

        // Add sample data
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->fromArray($data, null, "A{$row}");
            $row++;
        }

        // Style sample data
        $dataStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle("A2:D{$row}")->applyFromArray($dataStyle);

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions
        $instructions = [
            '',
            'PETUNJUK PENGISIAN:',
            '1. Nomor Seri Gabungan: Format standar kontainer (contoh: CBHU1234567)',
            '2. Ukuran: 20 atau 40 (feet)',
            '3. Tipe Kontainer: DRY atau REEFER',
            '4. Status: Tersedia atau Disewa',
            '',
            'CATATAN:',
            '- Pastikan data tidak ada yang kosong',
            '- Jangan mengubah format header (baris 1)',
            '- Hapus baris contoh ini sebelum import'
        ];

        $instructionRow = $row + 2;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue("A{$instructionRow}", $instruction);
            $instructionRow++;
        }

        // Style instructions
        $sheet->getStyle("A" . ($row + 3))->getFont()->setBold(true);
        $sheet->getStyle("A" . ($row + 8))->getFont()->setBold(true);

        return $spreadsheet;
    }
}
