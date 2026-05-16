<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportPranotaUangJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $pranotas;
    protected $startDate;
    protected $endDate;

    public function __construct($pranotas, $startDate, $endDate)
    {
        $this->pranotas = $pranotas;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->pranotas;
    }

    public function headings(): array
    {
        return [
            ['REPORT PRANOTA UANG JALAN'],
            ['Periode: ' . $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y')],
            [''],
            [
                'No',
                'Tanggal',
                'Nomor Pranota',
                'Periode Tagihan',
                'Jumlah Uang Jalan',
                'Penyesuaian',
                'Keterangan Penyesuaian',
                'Total Akhir',
                'Status',
                'Catatan',
                'Dibuat Oleh'
            ]
        ];
    }

    public function map($pranota): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $pranota->tanggal_pranota->format('d/m/Y'),
            $pranota->nomor_pranota,
            $pranota->periode_tagihan,
            (float)$pranota->jumlah_uang_jalan,
            (float)$pranota->penyesuaian,
            $pranota->keterangan_penyesuaian,
            (float)$pranota->total_with_penyesuaian,
            $pranota->status_text,
            $pranota->catatan,
            $pranota->creator->name ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        
        $lastRow = $sheet->getHighestRow();
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'] // Blue 600
                ]
            ],
            'A1:K' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
