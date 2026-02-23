<?php

namespace App\Exports;

use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\Gudang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BranchGudangKontainerExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $cabangNama;

    public function __construct($cabangNama)
    {
        $this->cabangNama = $cabangNama;
    }

    public function collection()
    {
        $gudangIds = Gudang::where('lokasi', $this->cabangNama)
            ->where('status', 'aktif')
            ->pluck('id');

        // Get kontainers join with gudang for names
        $kontainers = Kontainer::whereIn('gudangs_id', $gudangIds)
            ->with('gudang')
            ->orderBy('gudangs_id')
            ->orderBy('nomor_seri_gabungan')
            ->get()
            ->map(function($k) {
                return [
                    $k->gudang->nama_gudang ?? '-',
                    $k->nomor_seri_gabungan,
                    $k->ukuran ?? '-',
                    $k->tipe_kontainer ?? '-',
                ];
            });

        // Get stock_kontainers join with gudang for names
        $stockKontainers = StockKontainer::whereIn('gudangs_id', $gudangIds)
            ->with('gudang')
            ->orderBy('gudangs_id')
            ->orderBy('nomor_seri_gabungan')
            ->get()
            ->map(function($s) {
                return [
                    $s->gudang->nama_gudang ?? '-',
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
            'Gudang',
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
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);
                $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
