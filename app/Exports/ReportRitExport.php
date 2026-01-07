<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class ReportRitExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $suratJalans;
    protected $startDate;
    protected $endDate;

    public function __construct($suratJalans, $startDate, $endDate)
    {
        $this->suratJalans = $suratJalans;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->suratJalans->map(function($sj, $index) {
            // Format tanggal
            $tanggal = '-';
            if ($sj->tanggal_surat_jalan) {
                $tanggal = is_string($sj->tanggal_surat_jalan) 
                    ? Carbon::parse($sj->tanggal_surat_jalan)->format('d/m/Y')
                    : $sj->tanggal_surat_jalan->format('d/m/Y');
            } elseif ($sj->order && $sj->order->tanggal_order) {
                $tanggal = is_string($sj->order->tanggal_order)
                    ? Carbon::parse($sj->order->tanggal_order)->format('d/m/Y')
                    : $sj->order->tanggal_order->format('d/m/Y');
            }

            // No Surat Jalan
            $noSuratJalan = $sj->no_surat_jalan 
                ? $sj->no_surat_jalan 
                : ($sj->order ? $sj->order->nomor_order : '-');

            // Kegiatan
            $kegiatan = ucfirst(strtolower($sj->kegiatan ? $sj->kegiatan : 'tarik isi'));

            // Supir
            $supir = $sj->supir 
                ? $sj->supir 
                : ($sj->supir2 ? $sj->supir2 : '-');

            // Pengirim
            $pengirim = $sj->pengirimRelation 
                ? $sj->pengirimRelation->nama_pengirim 
                : ($sj->pengirim ? $sj->pengirim : '-');

            // Penerima/Tujuan
            $penerima = $sj->tujuanPengirimanRelation 
                ? $sj->tujuanPengirimanRelation->nama_tujuan 
                : ($sj->tujuan_pengiriman ? $sj->tujuan_pengiriman : '-');

            // Jenis Barang
            $jenisBarang = $sj->jenisBarangRelation 
                ? $sj->jenisBarangRelation->nama_barang 
                : ($sj->jenis_barang ? $sj->jenis_barang : '-');

            // Tipe
            $tipe = $sj->tipe_kontainer 
                ? $sj->tipe_kontainer 
                : ($sj->size ? $sj->size : ($sj->order ? $sj->order->tipe_kontainer : '-'));

            return [
                $index + 1,
                $tanggal,
                $noSuratJalan,
                $kegiatan,
                $supir,
                $sj->no_plat ? $sj->no_plat : '-',
                $pengirim,
                $penerima,
                $jenisBarang,
                $tipe,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No. Surat Jalan',
            'Kegiatan',
            'Supir',
            'No. Plat',
            'Pengirim',
            'Penerima',
            'Jenis Barang',
            'Tipe',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style header
                $event->sheet->getStyle('A1:J1')->applyFromArray([
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
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Add borders to all cells with data
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A1:J' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Center align nomor column
                $event->sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add title
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->setCellValue('A1', 'REPORT RIT');
                $event->sheet->setCellValue('A2', 'Periode: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));
                
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->mergeCells('A2:J2');
                
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Set row height for title rows
                $event->sheet->getRowDimension('1')->setRowHeight(25);
                $event->sheet->getRowDimension('2')->setRowHeight(20);
            },
        ];
    }
}
