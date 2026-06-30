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

class PranotaOngkosTrukExport2 implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings
{
    protected $pranota;

    public function __construct($pranota)
    {
        $this->pranota = $pranota;
    }

    public function collection()
    {
        return $this->pranota->items->map(function ($item, $index) {
            if ($item->type === 'SuratJalan' && $item->suratJalan) {
                $sj = $item->suratJalan;
                $tgl_sj = $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/M/Y') : '-';
                $nama_supir = $sj->supir ?: ($sj->supir2 ?: '-');
                $no_plat = $sj->no_plat ?? '-';
                $no_surat_jalan = $sj->no_surat_jalan;
                
                // Kegiatan, Muat, Tujuan, PT
                $kegiatan = 'Uang Jalan Muat';
                $muatan = $sj->jenis_barang ?? '-';
                $tujuan = $sj->tujuan_pengambilan ?? '-';
                $pt = $sj->pengirim ?? $sj->tujuan_pengiriman ?? '-';
                
                // Cari NIK
                $nik_supir = '-';
                if ($sj->supir) {
                    $k = \App\Models\Karyawan::where('nama_panggilan', $sj->supir)->orWhere('nama_lengkap', $sj->supir)->first();
                    if ($k) $nik_supir = $k->nik;
                }
                
                // Ongkos Truk
                $ongkos_truk = 0;
                if ($sj->tujuanPengambilanRelation) {
                    $sz = strtolower($sj->size ?? '');
                    $ongkos_truk = str_contains($sz, '40')
                        ? ($sj->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0)
                        : ($sj->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0);
                }
                if ($sj->tujuan_pengambilan == 'PULO GADUNG ( BESI SCRAP )') {
                    $ongkos_truk = 1050000;
                }

                // Uang Jalan & No Bukti
                $uang_jalan = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;
                $no_bukti = '-';
                if ($sj->uangJalan && count($sj->uangJalan->pranotaUangJalan) > 0) {
                    $buktis = collect();
                    foreach ($sj->uangJalan->pranotaUangJalan as $puj) {
                        if ($puj->pembayaranPranotaUangJalans) {
                            $buktis = $buktis->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                        }
                    }
                    $no_bukti = $buktis->filter()->unique()->implode(', ') ?: '-';
                }

            } elseif ($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                $sjb = $item->suratJalanBongkaran;
                $tgl_sj = $sjb->tanggal_surat_jalan ? $sjb->tanggal_surat_jalan->format('d/M/Y') : '-';
                $nama_supir = $sjb->supir ?: ($sjb->supir2 ?: '-');
                $no_plat = $sjb->no_plat ?? '-';
                $no_surat_jalan = $sjb->nomor_surat_jalan;

                // Kegiatan, Muat, Tujuan, PT
                $kegiatan = 'Uang Jalan Bongkar';
                $muatan = $sjb->jenis_barang ?? '-';
                $tujuan = $sjb->tujuan_pengambilan ?? '-';
                $pt = $sjb->pengirim ?? $sjb->tujuan_pengiriman ?? '-';

                // Cari NIK
                $nik_supir = '-';
                if ($sjb->supir) {
                    $k = \App\Models\Karyawan::where('nama_panggilan', $sjb->supir)->orWhere('nama_lengkap', $sjb->supir)->first();
                    if ($k) $nik_supir = $k->nik;
                }

                // Ongkos Truk
                $ongkos_truk = 0;
                if ($sjb->tujuanPengambilanRelation) {
                    $sz = strtolower($sjb->size ?? '');
                    $ongkos_truk = str_contains($sz, '40')
                        ? ($sjb->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0)
                        : ($sjb->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0);
                }
                if ($sjb->tujuan_pengambilan == 'PULO GADUNG ( BESI SCRAP )') {
                    $ongkos_truk = 1050000;
                }

                // Uang Jalan & No Bukti
                $uang_jalan = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;
                $no_bukti = '-';
                if ($sjb->uangJalan && count($sjb->uangJalan->pranotaUangJalan) > 0) {
                    $buktis = collect();
                    foreach ($sjb->uangJalan->pranotaUangJalan as $puj) {
                        if ($puj->pembayaranPranotaUangJalans) {
                            $buktis = $buktis->merge($puj->pembayaranPranotaUangJalans->pluck('nomor_accurate'));
                        }
                    }
                    $no_bukti = $buktis->filter()->unique()->implode(', ') ?: '-';
                }
            } else {
                $tgl_sj = '-';
                $nama_supir = '-';
                $no_plat = '-';
                $no_surat_jalan = '-';
                $kegiatan = '-';
                $muatan = '-';
                $tujuan = '-';
                $pt = '-';
                $nik_supir = '-';
                $ongkos_truk = 0;
                $uang_jalan = 0;
                $no_bukti = '-';
            }

            return [
                $nik_supir !== '-' ? "'".$nik_supir : '-',
                $tgl_sj,
                $nama_supir,
                $no_plat,
                $no_surat_jalan,
                $kegiatan,
                $muatan,
                $tujuan,
                $pt,
                $no_bukti,
                (float) $ongkos_truk,
                (float) $uang_jalan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NIK Supir',
            'Tgl.',
            'Nama',
            'Plat Mobil',
            'No Surat Jalan',
            'Kegiatan',
            'Muat',
            'Tujuan',
            'PT.',
            'No. Bukti',
            'Jumlah Ongkos Truck',
            'Cr',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => '#,##0',
            'L' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 4);
                $sheet->setCellValue('A1', 'PRANOTA ONGKOS TRUK');
                $sheet->setCellValue('A2', 'Nomor: ' . $this->pranota->no_pranota);
                $sheet->setCellValue('A3', 'Tanggal: ' . $this->pranota->tanggal_pranota->format('d/m/Y'));

                $lastCol = 'L';
                $headerRow = 5;
                $dataStartRow = 6;

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                
                $sheet->getStyle('A2:A3')->getFont()->setBold(true);

                // Style Header
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                $lastDataRow = $sheet->getHighestRow();
                $currentRow = $lastDataRow + 1;

                // Subtotal
                $sheet->setCellValue("J{$currentRow}", 'Subtotal');
                $sheet->setCellValue("K{$currentRow}", "=SUM(K{$dataStartRow}:K{$lastDataRow})");
                $sheet->setCellValue("L{$currentRow}", "=SUM(L{$dataStartRow}:L{$lastDataRow})");
                $sheet->getStyle("J{$currentRow}:L{$currentRow}")->getFont()->setBold(true);
                $currentRow++;
                
                // Adjustment
                if ($this->pranota->adjustment != 0) {
                    $sheet->setCellValue("J{$currentRow}", 'Adjustment');
                    $sheet->setCellValue("K{$currentRow}", (float) $this->pranota->adjustment);
                    if ($this->pranota->keterangan) {
                        $sheet->setCellValue("L{$currentRow}", '('.$this->pranota->keterangan.')');
                    }
                    $sheet->getStyle("J{$currentRow}:K{$currentRow}")->getFont()->setBold(true);
                    $currentRow++;
                }

                // Total
                $sheet->setCellValue("J{$currentRow}", 'TOTAL NOMINAL');
                $sheet->setCellValue("K{$currentRow}", (float) $this->pranota->total_nominal);
                $sheet->getStyle("J{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'],
                    ],
                ]);
                
                // Style data borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$currentRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}
