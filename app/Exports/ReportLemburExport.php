<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportLemburExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    protected $no = 1;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Tanda Terima',
            'No Pranota',
            'Tipe',
            'No SJ',
            'Pengirim',
            'Supir',
            'Plat',
            'Status',
            'Status Pranota',
        ];
    }

    public function map($sj): array
    {
        $status = [];
        if ($sj->lembur) {
            $status[] = 'Lembur';
        }
        if ($sj->nginap) {
            $status[] = 'Nginap';
        }

        return [
            $this->no++,
            $sj->report_date ? Carbon::parse($sj->report_date)->format('d/M/Y') : '-',
            $sj->no_pranota ?: '-',
            $sj->type_surat,
            $sj->no_surat_jalan,
            $sj->pengirim ?: '-',
            $sj->supir,
            $sj->no_plat,
            implode(', ', $status),
            $sj->sudah_pranota ? 'Sudah Pranota' : 'Belum Pranota',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'], // blue-800
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
