<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportKerjaSupirBatamExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $waybills;
    protected $startDate;
    protected $endDate;
    protected $totalRit;

    public function __construct($waybills, $startDate, $endDate, $totalRit)
    {
        $this->waybills = $waybills;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalRit = $totalRit;
    }

    public function collection()
    {
        // Wrap array in collection if it's an array, otherwise return as is
        return collect($this->waybills);
    }

    public function headings(): array
    {
        return [
            ['REPORT KERJA SUPIR BATAM'],
            ['Periode: ' . \Carbon\Carbon::parse($this->startDate)->format('d/m/Y') . ' s/d ' . \Carbon\Carbon::parse($this->endDate)->format('d/m/Y')],
            [''],
            [
                'No',
                'Tanggal',
                'Supir',
                'Tipe Pekerjaan',
                'No. Dokumen',
                'No. Kontainer',
                'Uang Jalan / Biaya (Rp)',
            ],
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        // $item is an array because we built it in the controller
        return [
            $index,
            $item['tanggal'],
            $item['supir'],
            $item['tipe'],
            $item['no_dokumen'],
            $item['no_kontainer'],
            (float) $item['uang_jalan'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        $lastRow = $sheet->getHighestRow();
        
        // Add total row at the bottom
        $sheet->setCellValue('A' . ($lastRow + 1), 'TOTAL PENDAPATAN SUPIR');
        $sheet->mergeCells('A' . ($lastRow + 1) . ':F' . ($lastRow + 1));
        $sheet->setCellValue('G' . ($lastRow + 1), (float) $this->totalRit);
        
        $lastRow = $sheet->getHighestRow();

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo 600
                ],
            ],
            $lastRow => [
                'font' => ['bold' => true],
            ],
            'A1:G'.$lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'G5:G'.$lastRow => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ],
            'A'.$lastRow => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ]
            ]
        ];
    }
}
