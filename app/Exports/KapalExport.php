<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KapalExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $rows;

    public function __construct($kapals)
    {
        $this->rows = collect($kapals)->map(function ($kapal, $index) {
            $totalKapasitas = ($kapal->kapasitas_kontainer_palka ?? 0) + ($kapal->kapasitas_kontainer_deck ?? 0);

            return [
                $index + 1,
                $kapal->kode ?? '',
                $kapal->kode_kapal ?? '',
                $kapal->nama_kapal ?? '',
                $kapal->nickname ?? '',
                $kapal->pelayaran ?? '',
                $kapal->kapasitas_kontainer_palka ?? '',
                $kapal->kapasitas_kontainer_deck ?? '',
                $kapal->gross_tonnage ?? '',
                $totalKapasitas > 0 ? $totalKapasitas : '',
                $kapal->catatan ?? '',
                ucfirst($kapal->status ?? ''),
                $kapal->created_at ? $kapal->created_at->format('d/M/Y H:i') : '',
                $kapal->updated_at ? $kapal->updated_at->format('d/M/Y H:i') : ''
            ];
        });
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Kode Kapal',
            'Nama Kapal',
            'Nickname',
            'Pelayaran (Pemilik)',
            'Kapasitas Palka',
            'Kapasitas Deck',
            'Gross Tonnage',
            'Total Kapasitas',
            'Catatan',
            'Status',
            'Tanggal Dibuat',
            'Tanggal Diperbarui'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:N1")->getFont()->setBold(true);
                $sheet->getStyle("A1:N{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
        ];
    }
}