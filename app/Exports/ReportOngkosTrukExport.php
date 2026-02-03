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
                $item['nama_lengkap_supir'],
                $item['nik_supir'] ? "'" . $item['nik_supir'] : '-',
                $item['rit_supir'],
                $item['nama_lengkap_kenek'],
                $item['nik_kenek'] ? "'" . $item['nik_kenek'] : '-',
                $item['rit_kenek'],
                $item['tujuan'],
                (float)$item['ongkos_truck'],
                (float)$item['uang_jalan'],
                $item['nomor_bukti'] ?? '-',
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
            'NIK Supir',
            'Rit Supir',
            'Kenek',
            'NIK Kenek',
            'Rit Kenek',
            'Tujuan',
            'Ongkos Truk',
            'Uang Jalan',
            'Nomor Bukti',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => '#,##0',
            'M' => '#,##0',
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
                
                $lastCol = 'N';
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

                $sheet->setCellValue("K{$totalRow}", 'TOTAL');
                // Sum Rit Supir
                $sheet->setCellValue("G{$totalRow}", "=SUM(G{$dataStartRow}:G{$lastDataRow})");
                // Sum Rit Kenek
                $sheet->setCellValue("J{$totalRow}", "=SUM(J{$dataStartRow}:J{$lastDataRow})");
                // Sum Ongkos Truk
                $sheet->setCellValue("L{$totalRow}", "=SUM(L{$dataStartRow}:L{$lastDataRow})");
                // Sum Uang Jalan
                $sheet->setCellValue("M{$totalRow}", "=SUM(M{$dataStartRow}:M{$lastDataRow})");
                
                // Style the entire table borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Style Total Row
                $sheet->getStyle("K{$totalRow}:M{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("K{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G{$totalRow}")->getFont()->setBold(true); // Bold Rit Supir Total
                $sheet->getStyle("J{$totalRow}")->getFont()->setBold(true); // Bold Rit Kenek Total
                $sheet->getStyle("L{$dataStartRow}:M{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Auto height for rows
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(20);
            },
        ];
    }
}
