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
                $m->nomor_urut ?? '-',
                $m->nomor_bl,
                $m->nomor_tanda_terima ?? '-',
                $m->nomor_kontainer,
                $m->no_seal ?? '-',
                $m->tipe_kontainer,
                $m->size_kontainer,
                trim(($m->kuantitas ?? '') . ' ' . ($m->satuan ?? '') . ' ' . ($m->nama_barang ?? '')),
                $m->pengirim,
                $m->penerima,
                $m->prospek && $m->prospek->tandaTerima ? $m->prospek->tandaTerima->meter_kubik : ($m->volume ?? '-'),
                $m->prospek && $m->prospek->tandaTerima ? $m->prospek->tandaTerima->tonase : ($m->tonnage ?? '-'),
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Urut',
            'No. BL',
            'No. Tanda Terima',
            'MARK AND NUMBERS',
            'SEAL NO.',
            'Tipe',
            'Size',
            'DESCRIPTION OF GOODS',
            'Pengirim',
            'Penerima',
            'Volume',
            'Tonase',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:M1')->getFont()->setBold(true);
                $sheet->getStyle('A1:M1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add cell borders to all data
                $lastRow = count($this->manifests) + 1;
                $sheet->getStyle('A1:M' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        ];
    }
}
