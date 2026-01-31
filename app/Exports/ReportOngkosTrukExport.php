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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
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
                $item['keterangan'],
                $item['tujuan'],
                $item['ongkos_truck'],
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
            'Keterangan',
            'Tujuan',
            'Ongkos Truk',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $event->sheet->getHighestRow();
                $lastCol = 'H';

                // Style header
                $event->sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
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

                // Add Row Total
                $totalRow = $lastRow + 1;
                $event->sheet->setCellValue('G' . $totalRow, 'TOTAL');
                $event->sheet->setCellValue('H' . $totalRow, '=SUM(H2:H' . $lastRow . ')');
                
                $event->sheet->getStyle('A1:' . $lastCol . $totalRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $event->sheet->getStyle('G' . $totalRow . ':H' . $totalRow)->getFont()->setBold(true);
                $event->sheet->getStyle('H2:H' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Add Title
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->setCellValue('A1', 'LAPORAN ONGKOS TRUK');
                $event->sheet->setCellValue('A2', 'Periode: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));
                
                $event->sheet->mergeCells('A1:' . $lastCol . '1');
                $event->sheet->mergeCells('A2:' . $lastCol . '2');
                
                $event->sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                $event->sheet->getStyle('A1')->getFont()->setSize(14);
            },
        ];
    }
}
