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

class ReportOngkosTrukExport2 implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings
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
        return $this->data->map(function ($item, $index) {
            return [
                $item['nik_supir'] ? "'".$item['nik_supir'] : '-',
                $item['tanggal'],
                $item['nama_lengkap_supir'],
                $item['no_plat'],
                $item['no_surat_jalan'],
                $item['kegiatan_str'] ?? ($item['keterangan'] ?? '-'),
                $item['muatan_str'] ?? '-',
                $item['tujuan'],
                $item['pt_str'] ?? '-',
                $item['keterangan_lengkap'] ?? '-',
                $item['nomor_bukti'] ?? '-',
                (float) $item['ongkos_truck'],
                (float) $item['uang_jalan'],
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
            'Keterangan',
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

                $sheet->insertNewRowBefore(1, 3);
                $sheet->setCellValue('A1', 'Ongkos Truck per tgl ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));

                $lastCol = 'L';
                $headerRow = 4;
                $dataStartRow = 5;

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

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
                
                // Style data borders
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastDataRow}")->applyFromArray([
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
