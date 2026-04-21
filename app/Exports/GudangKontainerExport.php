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
    protected $type;

    public function __construct($gudangId, $type = 'all')
    {
        $this->gudangId = $gudangId;
        $this->type = $type;
    }

    public function collection()
    {
        $kontainers = collect([]);
        $gudangIdForHistory = ($this->gudangId === 'none' || $this->gudangId === '') ? null : $this->gudangId;

        if ($this->type === 'all' || $this->type === 'sewa') {
            // Get kontainers
            $queryK = Kontainer::orderBy('nomor_seri_gabungan');
            if ($this->gudangId === null || $this->gudangId === 'none' || $this->gudangId === '') {
                $queryK->whereNull('gudangs_id');
            } else {
                $queryK->where('gudangs_id', $this->gudangId);
            }
            
            $kontainers = $queryK->get()->map(function($k) use ($gudangIdForHistory) {
                    $tanggalMasuk = \App\Models\HistoryKontainer::where('nomor_kontainer', $k->nomor_seri_gabungan)
                        ->where('gudang_id', $gudangIdForHistory)
                        ->max('tanggal_kegiatan');

                    return [
                        $k->nomor_seri_gabungan,
                        $k->ukuran ?? '-',
                        $k->tipe_kontainer ?? '-',
                        $tanggalMasuk ? \Carbon\Carbon::parse($tanggalMasuk)->format('d/m/Y') : '-',
                    ];
                });
        }

        $stockKontainers = collect([]);
        if ($this->type === 'all' || $this->type === 'stock') {
            // Get stock_kontainers
            $queryS = StockKontainer::orderBy('nomor_seri_gabungan');
            if ($this->gudangId === null || $this->gudangId === 'none' || $this->gudangId === '') {
                $queryS->whereNull('gudangs_id');
            } else {
                $queryS->where('gudangs_id', $this->gudangId);
            }

            $stockKontainers = $queryS->get()->map(function($s) use ($gudangIdForHistory) {
                    $tanggalMasuk = \App\Models\HistoryKontainer::where('nomor_kontainer', $s->nomor_seri_gabungan)
                        ->where('gudang_id', $gudangIdForHistory)
                        ->max('tanggal_kegiatan');

                    return [
                        $s->nomor_seri_gabungan,
                        $s->ukuran ?? '-',
                        $s->tipe_kontainer ?? '-',
                        $tanggalMasuk ? \Carbon\Carbon::parse($tanggalMasuk)->format('d/m/Y') : '-',
                    ];
                });
        }

        return $kontainers->concat($stockKontainers);
    }

    public function headings(): array
    {
        return [
            'Nomor Kontainer',
            'Ukuran',
            'Tipe',
            'Tanggal Masuk',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);
                $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
