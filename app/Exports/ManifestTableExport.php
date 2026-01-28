<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ManifestTableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $manifests;

    public function __construct($manifests)
    {
        $this->manifests = $manifests;
    }

    public function collection()
    {
        $rows = $this->manifests->map(function($m, $index) {
            return [
                $index + 1,
                $m->nomor_bl,
                $m->nomor_tanda_terima ?? '-',
                $m->nomor_kontainer,
                $m->no_seal ?? '-',
                $m->tipe_kontainer . " - " . $m->size_kontainer . "'",
                $m->nama_barang,
                $m->pengirim,
                $m->penerima,
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. BL',
            'No. Tanda Terima',
            'No. Kontainer',
            'No. Seal',
            'Tipe & Size',
            'Nama Barang',
            'Pengirim',
            'Penerima',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:I1')->getFont()->setBold(true);
                $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add cell borders to all data
                $lastRow = count($this->manifests) + 1;
                $sheet->getStyle('A1:I' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        ];
    }
}
