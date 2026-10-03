<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PranotaLemburKaryawanExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings
{
    protected $pranota;

    public function __construct($pranota)
    {
        $this->pranota = $pranota;
    }

    public function collection()
    {
        return $this->pranota->karyawans->map(function ($d, $index) {
            $karyawan = $d->karyawan;

            $dates = $d->tanggal_lembur;
            if (is_string($dates)) {
                $dates = json_decode($dates, true);
            }
            $hariLemburStr = '-';
            if (is_array($dates) && count($dates) > 0) {
                $formatted = array_map(function ($tgl) {
                    $c = \Carbon\Carbon::parse($tgl)->locale('id');

                    return $c->isoFormat('ddd, D/M');
                }, $dates);
                $hariLemburStr = count($dates).' Hari: '.implode(', ', $formatted);
            }

            return [
                'no' => $index + 1,
                'nik' => $karyawan->nik ?? '-',
                'nama' => $karyawan->nama_lengkap ?? ($karyawan->nama_panggilan ?? '-'),
                'penempatan' => $karyawan->penempatan ?? '-',
                'divisi' => $karyawan->divisi ?? '-',
                'hari_lembur' => $hariLemburStr,
                'jam_lembur' => $d->jam_lembur,
                'nominal_awal' => (float) $d->nominal_awal,
                'adjustment' => (float) $d->adjustment,
                'total_akhir' => (float) $d->total_akhir,
                'catatan' => $d->catatan ?: '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Karyawan',
            'Penempatan',
            'Divisi',
            'Hari Lembur',
            'Jam Lembur',
            'Nominal Awal',
            'Adjustment',
            'Total Akhir',
            'Catatan',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'K';
                $headerRow = 6;
                $dataStartRow = 7;

                // Fixed width for No column
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(6);

                // Insert metadata header rows
                $sheet->insertNewRowBefore(1, 5);
                $sheet->setCellValue('A1', 'PRANOTA LEMBUR KARYAWAN');
                $sheet->setCellValue('A2', 'Nomor Pranota: '.$this->pranota->nomor_pranota);
                $sheet->setCellValue('A3', 'Tanggal: '.($this->pranota->tanggal_pranota ? $this->pranota->tanggal_pranota->format('d/m/Y') : '-'));
                $creatorName = $this->pranota->creator->name ?? 'System';
                $sheet->setCellValue('A4', 'Dibuat Oleh: '.$creatorName.' | Total Karyawan: '.$this->pranota->karyawans->count().' Orang');

                // Title style
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2:A4')->getFont()->setBold(true);

                // Table Header styling (Blue-800)
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E40AF'], // Blue 800
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Find Last Data Row and Add Summary Rows
                $lastDataRow = $sheet->getHighestRow();
                $currentRow = $lastDataRow + 1;

                // Subtotal row
                $sheet->setCellValue('G'.$currentRow, 'Subtotal:');
                $sheet->setCellValue('H'.$currentRow, "=SUM(H{$dataStartRow}:H{$lastDataRow})");
                $sheet->setCellValue('I'.$currentRow, "=SUM(I{$dataStartRow}:I{$lastDataRow})");
                $sheet->setCellValue('J'.$currentRow, "=SUM(J{$dataStartRow}:J{$lastDataRow})");
                $sheet->getStyle("G{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;

                // Adjustment Row (if any)
                if ((float) $this->pranota->adjustment != 0) {
                    $sheet->setCellValue('G'.$currentRow, 'Adjustment:');
                    $sheet->setCellValue('J'.$currentRow, (float) $this->pranota->adjustment);
                    $sheet->getStyle("G{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                    $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $currentRow++;
                }

                // Total Row
                $sheet->setCellValue('G'.$currentRow, 'TOTAL:');
                $sheet->setCellValue('J'.$currentRow, (float) $this->pranota->total_setelah_adjustment);
                $sheet->getStyle("G{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEF08A'], // Yellow 200
                    ],
                ]);
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Table Borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$currentRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Alignments
                $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$dataStartRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$dataStartRow}:G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$dataStartRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
