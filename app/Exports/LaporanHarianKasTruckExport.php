<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanHarianKasTruckExport implements FromArray, WithEvents, WithTitle
{
    public function __construct(private Collection $uangJalans, private string $tanggal) {}

    public function array(): array
    {
        $rows = [
            ['Laporan Harian Kas Truck', '', '', '', '', '', '', '', '', ''],
            ['No', 'Tgl.', 'Nama', '', 'No Surat Jalan', 'Kegiatan', 'Tujuan', '', '', 'CO'],
            ['', '', 'Nama', 'Plat Mob', '', '', 'Muat', 'Tujuan', 'PT.', ''],
        ];

        foreach ($this->uangJalans as $index => $uangJalan) {
            $suratJalan = $uangJalan->suratJalan;
            $order = $suratJalan?->order;
            $namaBarang = $suratJalan?->jenis_barang ?: $order?->nama_barang;
            if (is_array($namaBarang)) {
                $namaBarang = implode(', ', array_filter($namaBarang));
            }
            $namaBarang = $namaBarang ?: $order?->jenisBarang?->nama_barang ?: $suratJalan?->jenisBarangRelation?->nama_barang;

            $rows[] = [
                $index + 1,
                Carbon::parse($this->tanggal)->format('d M y'),
                $suratJalan?->supir ?: '-',
                $suratJalan?->no_plat ?: '-',
                $suratJalan?->no_surat_jalan ?: '-',
                'Uang Jalan',
                $namaBarang ?: '-',
                $suratJalan?->tujuan_pengambilan ?: $suratJalan?->tujuanPengambilanRelation?->ke ?: '-',
                $suratJalan?->pengirim ?: '-',
                $order?->nomor_order ?: $suratJalan?->no_pemesanan ?: '-',
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Kas Truck';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max(3, $this->uangJalans->count() + 3);

                $sheet->mergeCells('A1:J1');
                foreach (['A2:A3', 'B2:B3', 'C2:D2', 'E2:E3', 'F2:F3', 'G2:I2', 'J2:J3'] as $range) {
                    $sheet->mergeCells($range);
                }

                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => ['name' => 'Times New Roman', 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("A2:J{$lastRow}")->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2:J3')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);
                $sheet->getStyle("A4:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("A1:J{$lastRow}")->getAlignment()->setWrapText(false);
                $sheet->setAutoFilter("A3:J{$lastRow}");
                $sheet->freezePane('A4');

                foreach (['A' => 6, 'B' => 13, 'C' => 20, 'D' => 15, 'E' => 18, 'F' => 15, 'G' => 24, 'H' => 18, 'I' => 32, 'J' => 18] as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(21);
                $sheet->getRowDimension(3)->setRowHeight(21);
            },
        ];
    }
}
