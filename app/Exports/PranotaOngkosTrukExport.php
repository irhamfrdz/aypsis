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

class PranotaOngkosTrukExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected $pranota;

    public function __construct($pranota)
    {
        $this->pranota = $pranota;
    }

    public function collection()
    {
        return $this->pranota->items->map(function($item, $index) {
            $tujuan = '-';
            $size = '-';
            if ($item->type === 'SuratJalan' && $item->suratJalan) {
                $tujuan = $item->suratJalan->tujuanPengambilanRelation->ke ?? $item->suratJalan->tujuan_pengambilan ?? '-';
                $size = $item->suratJalan->size ?? '-';
            } elseif ($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                $tujuan = $item->suratJalanBongkaran->tujuanPengambilanRelation->ke ?? $item->suratJalanBongkaran->tujuan_pengambilan ?? '-';
                $size = $item->suratJalanBongkaran->size ?? '-';
            }

            return [
                $index + 1,
                $item->no_surat_jalan,
                $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                $tujuan,
                $size,
                (float)$item->nominal,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'No Surat Jalan',
            'Tanggal',
            'Tujuan',
            'Size',
            'Nominal',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert title rows
                $sheet->insertNewRowBefore(1, 5);
                $sheet->setCellValue('A1', 'PRANOTA ONGKOS TRUK');
                $sheet->setCellValue('A2', 'Nomor: ' . $this->pranota->no_pranota);
                $sheet->setCellValue('A3', 'Tanggal: ' . $this->pranota->tanggal_pranota->format('d/m/Y'));
                if ($this->pranota->keterangan) {
                    $sheet->setCellValue('A4', 'Keterangan: ' . $this->pranota->keterangan);
                    $sheet->getStyle("A4")->getFont()->setBold(true);
                }
                
                $lastCol = 'F';
                $headerRow = 6;
                $dataStartRow = 7;
                
                // Merge and style titles
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->getStyle("A2:A3")->getFont()->setBold(true);

                // Style Header
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Find Last Data Row and Add Summary Rows
                $lastDataRow = $sheet->getHighestRow();
                $currentRow = $lastDataRow + 1;

                // Subtotal
                $sheet->setCellValue("E{$currentRow}", 'Subtotal');
                $sheet->setCellValue("F{$currentRow}", "=SUM(F{$dataStartRow}:F{$lastDataRow})");
                $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getFont()->setBold(true);
                $currentRow++;

                // Adjustment
                if ($this->pranota->adjustment != 0) {
                    $sheet->setCellValue("E{$currentRow}", 'Adjustment');
                    $sheet->setCellValue("F{$currentRow}", (float)$this->pranota->adjustment);
                    if ($this->pranota->keterangan) {
                        $sheet->setCellValue("G{$currentRow}", "(" . $this->pranota->keterangan . ")");
                    }
                    $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getFont()->setBold(true);
                    $currentRow++;
                }

                // Total
                $sheet->setCellValue("E{$currentRow}", 'TOTAL');
                $sheet->setCellValue("F{$currentRow}", (float)$this->pranota->total_nominal);
                $sheet->getStyle("E{$currentRow}:F{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'],
                    ],
                ]);

                // Table borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$currentRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                $sheet->getStyle("F{$dataStartRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
