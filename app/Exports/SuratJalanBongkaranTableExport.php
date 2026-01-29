<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SuratJalanBongkaranTableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $mode;

    public function __construct($data, $mode = 'manifest')
    {
        $this->data = $data;
        $this->mode = $mode;
    }

    public function collection()
    {
        if ($this->mode === 'surat_jalan') {
            return $this->data->map(function($sj, $index) {
                return [
                    $index + 1,
                    $sj->manifest->nomor_urut ?? '-',
                    $sj->nomor_surat_jalan ?: '-',
                    $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-',
                    $sj->term ?: '-',
                    $sj->supir ?: '-',
                    $sj->no_plat ?: '-',
                    $sj->no_kontainer ?: '-',
                    $sj->jenis_barang ?: '-',
                ];
            });
        } else {
            // Manifest mode
            return $this->data->map(function($m, $index) {
                return [
                    $index + 1,
                    $m->nomor_urut ?? '-',
                    $m->nomor_bl ?: '-',
                    $m->nomor_kontainer ?: '-',
                    $m->no_seal ?: '-',
                    $m->size_kontainer ?: '-',
                    $m->term ? ($m->term_nama ? $m->term . ' - ' . $m->term_nama : $m->term) : '-',
                    $m->nama_barang ?: '-',
                    $m->penerima ?: '-',
                ];
            });
        }
    }

    public function headings(): array
    {
        if ($this->mode === 'surat_jalan') {
            return [
                'No',
                'No. Urut',
                'Nomor Surat Jalan',
                'Tanggal',
                'Term',
                'Supir',
                'No Plat',
                'Nomor Container',
                'Jenis Barang',
            ];
        } else {
            return [
                'No',
                'No. Urut',
                'Nomor BL',
                'Nomor Container',
                'No Seal',
                'Size',
                'Term',
                'Nama Barang',
                'Penerima',
            ];
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $columnCount = $this->mode === 'surat_jalan' ? 'I' : 'I'; // Both have 9 columns
                $headerRange = 'A1:' . $columnCount . '1';
                
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');

                // Add cell borders to all data
                $lastRow = count($this->data) + 1;
                if ($lastRow > 1) {
                    $sheet->getStyle('A1:' . $columnCount . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }
            }
        ];
    }
}
