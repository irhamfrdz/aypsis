<?php

namespace App\Exports;

use App\Models\Kontainer;
use App\Models\StockKontainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GudangKontainerExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $gudangId;

    public function __construct($gudangId)
    {
        $this->gudangId = $gudangId;
    }

    public function collection()
    {
        // Get kontainers
        $kontainers = Kontainer::where('gudangs_id', $this->gudangId)
            ->orderBy('nomor_seri_gabungan')
            ->get()
            ->map(function($k) {
                return [
                    $k->nomor_seri_gabungan,
                    $k->ukuran ?? '-',
                    $k->tipe_kontainer ?? '-',
                ];
            });

        // Get stock_kontainers
        $stockKontainers = StockKontainer::where('gudangs_id', $this->gudangId)
            ->orderBy('nomor_seri_gabungan')
            ->get()
            ->map(function($s) {
                return [
                    $s->nomor_seri_gabungan,
                    $s->ukuran ?? '-',
                    $s->tipe_kontainer ?? '-',
                ];
            });

        return $kontainers->concat($stockKontainers);
    }

    public function headings(): array
    {
        return [
            'Nomor Kontainer',
            'Ukuran',
            'Tipe',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:C1')->getFont()->setBold(true);
                $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
