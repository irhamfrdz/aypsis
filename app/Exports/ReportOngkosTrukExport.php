<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ReportOngkosTrukExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
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
        return $this->data->map(function($item, $index) {
            return [
                $index + 1,
                $item['tanggal'],
                $item['no_surat_jalan'],
                $item['no_plat'],
                $item['supir'],
                $item['tujuan'],
                (float)$item['ongkos_truck'], // Ensure it's a number
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
            'Tujuan',
            'Ongkos Truk',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // 1. Insert Title rows FIRST to get correct row numbers
                $sheet->insertNewRowBefore(1, 3);
                $sheet->setCellValue('A1', 'LAPORAN ONGKOS TRUK');
                $sheet->setCellValue('A2', 'Periode: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));
                
                $lastCol = 'G';
                $headerRow = 4; // Now headers are at row 4
                $dataStartRow = 5; // Data starts at row 5
                
                // Merge title
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle("A1:A2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle("A2")->getFont()->setBold(true);

                // 2. Style Header (Row 4)
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // 3. Find Last Data Row and Add Total
                $lastDataRow = $sheet->getHighestRow();
                $totalRow = $lastDataRow + 1;

                $sheet->setCellValue("F{$totalRow}", 'TOTAL');
                // SUM from G5 to G{lastDataRow}
                $sheet->setCellValue("G{$totalRow}", "=SUM(G{$dataStartRow}:G{$lastDataRow})");
                
                // Style the entire table borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Style Total Row
                $sheet->getStyle("F{$totalRow}:G{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("G{$dataStartRow}:G{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Auto height for rows
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(20);
            },
        ];
    }
}
