<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportSuratJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->data->map(function ($item, $index) {
            return [
                $index + 1,
                $item['tanggal']->format('d/m/Y'),
                $item['no_surat_jalan'],
                $item['no_plat'],
                $item['supir'],
                $item['kenek'],
                $item['rute'],
                (float)$item['uang_jalan'],
                $item['nomor_bukti'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No Surat Jalan',
            'Plat Mobil',
            'Supir',
            'Kenek',
            'Rute',
            'Uang Jalan',
            'Nomor Bukti',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '"Rp "#,##0_-', // Format Currency untuk Uang Jalan (Kolom H)
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'I'; // Kolom I: Nomor Bukti

                // Header Data Starts at Row 4 (Setelah judul report)
                $dataStartRow = 4;
                
                // Insert Rows for Title
                $sheet->insertNewRowBefore(1, 3);
                
                // Judul Report
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A1', 'LAPORAN SURAT JALAN');
                $sheet->setCellValue('A2', 'Periode: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));
                
                // Style Judul
                $sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Style Header Table
                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataStartRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0']
                    ],
                ]);

                // Style Seluruh Tabel
                $lastDataRow = $lastRow + 3; // +3 karena insert 3 row di atas
                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$lastDataRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Total Row
                $totalRow = $lastDataRow + 1;
                $sheet->setCellValue("G{$totalRow}", 'TOTAL');
                $sheet->setCellValue("H{$totalRow}", "=SUM(H{$dataStartRow}:H{$lastDataRow})");
                
                // Style Total Row
                $sheet->getStyle("G{$totalRow}:H{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->getStyle("H{$totalRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0_-');
                
                // Alignments
                $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $sheet->getStyle("B{$dataStartRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal
                $sheet->getStyle("H{$dataStartRow}:H{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Uang Jalan
                
            },
        ];
    }
}
