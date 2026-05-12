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
            $no_plat = '-';
            $tgl_sj = $item->tanggal ? $item->tanggal->format('d/m/Y') : '-';
            $tgl_tt = '-';
            $no_bukti = '-';
            $ongkos_truk = 0;
            $uang_jalan = 0;

            if ($item->type === 'SuratJalan' && $item->suratJalan) {
                $sj = $item->suratJalan;
                $tujuan = $sj->tujuanPengambilanRelation->ke ?? $sj->tujuan_pengambilan ?? '-';
                $size = $sj->size ?? '-';
                $no_plat = $sj->no_plat ?? '-';
                $tgl_sj = $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/M/Y') : $tgl_sj;
                
                // Ongkos Truck Logic
                if ($sj->tujuanPengambilanRelation) {
                    $sz = strtolower($sj->size ?? '');
                    $ongkos_truk = str_contains($sz, '40') 
                        ? ($sj->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0)
                        : ($sj->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0);
                }
                if ($sj->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
                    $ongkos_truk = 1050000;
                }

                // Uang Jalan & No Bukti
                $uj = $sj->uangJalan;
                $uang_jalan = $uj ? $uj->jumlah_total : 0;
                if ($uj && count($uj->pranotaUangJalan) > 0) {
                    $buktis = collect();
                    foreach ($uj->pranotaUangJalan as $puj) {
                        if ($puj->pembayaranPranotaUangJalans) {
                            $buktis = $buktis->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                        }
                    }
                    $no_bukti = $buktis->filter()->unique()->implode(', ') ?: '-';
                }

                // Tgl TT Logic
                if ($sj->tanggal_tanda_terima) {
                    $tgl_tt = $sj->tanggal_tanda_terima->format('d/M/Y');
                } elseif ($sj->tandaTerima && $sj->tandaTerima->tanggal) {
                    $tgl_tt = $sj->tandaTerima->tanggal->format('d/M/Y');
                }
            } elseif ($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                $sjb = $item->suratJalanBongkaran;
                $tujuan = $sjb->tujuanPengambilanRelation->ke ?? $sjb->tujuan_pengambilan ?? '-';
                $size = $sjb->size ?? '-';
                $no_plat = $sjb->no_plat ?? '-';
                $tgl_sj = $sjb->tanggal_surat_jalan ? $sjb->tanggal_surat_jalan->format('d/M/Y') : $tgl_sj;
                
                // Ongkos Truck Logic
                if ($sjb->tujuanPengambilanRelation) {
                    $sz = strtolower($sjb->size ?? '');
                    $ongkos_truk = str_contains($sz, '40') 
                        ? ($sjb->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0)
                        : ($sjb->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0);
                }
                if ($sjb->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
                    $ongkos_truk = 1050000;
                }

                // Uang Jalan & No Bukti
                $ujb = $sjb->uangJalan;
                $uang_jalan = $ujb ? $ujb->jumlah_total : 0;
                if ($ujb && count($ujb->pranotaUangJalan) > 0) {
                    $buktis = collect();
                    foreach ($ujb->pranotaUangJalan as $puj) {
                        if ($puj->pembayaranPranotaUangJalans) {
                            $buktis = $buktis->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                        }
                    }
                    $no_bukti = $buktis->filter()->unique()->implode(', ') ?: '-';
                }

                // For bongkaran, check relation
                if ($sjb->tandaTerima && $sjb->tandaTerima->tanggal_tanda_terima) {
                    $tgl_tt = $sjb->tandaTerima->tanggal_tanda_terima->format('d/M/Y');
                }
            }

            return [
                $index + 1,
                $tgl_sj,
                $tgl_tt,
                $item->no_surat_jalan,
                $no_bukti,
                $no_plat,
                $tujuan,
                $size,
                (float)$ongkos_truk,
                (float)$uang_jalan,
                (float)$item->nominal,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tgl SJ',
            'Tgl TT',
            'No Surat Jalan',
            'No Bukti',
            'No Plat',
            'Tujuan',
            'Size',
            'Ongkos',
            'UJ',
            'Nominal',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
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
                
                $lastCol = 'K';
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
                $sheet->setCellValue("J{$currentRow}", 'Subtotal');
                $sheet->setCellValue("K{$currentRow}", "=SUM(K{$dataStartRow}:K{$lastDataRow})");
                $sheet->getStyle("J{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                $currentRow++;

                // Adjustment
                if ($this->pranota->adjustment != 0) {
                    $sheet->setCellValue("J{$currentRow}", 'Adjustment');
                    $sheet->setCellValue("K{$currentRow}", (float)$this->pranota->adjustment);
                    if ($this->pranota->keterangan) {
                        $sheet->setCellValue("L{$currentRow}", "(" . $this->pranota->keterangan . ")");
                    }
                    $sheet->getStyle("J{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                    $currentRow++;
                }

                // Total
                $sheet->setCellValue("J{$currentRow}", 'TOTAL');
                $sheet->setCellValue("K{$currentRow}", (float)$this->pranota->total_nominal);
                $sheet->getStyle("J{$currentRow}:K{$currentRow}")->applyFromArray([
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

                $sheet->getStyle("I{$dataStartRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
