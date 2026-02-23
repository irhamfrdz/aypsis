<?php

namespace App\Exports;

use App\Models\SuratJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CheckpointKontainerKeluarExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SuratJalan::whereNotNull('tanggal_checkpoint')
            ->orderBy('tanggal_checkpoint', 'desc');

        // Filter by date range if provided
        if (!empty($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal_checkpoint', '>=', $this->filters['tanggal_dari']);
        }

        if (!empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_checkpoint', '<=', $this->filters['tanggal_sampai']);
        }

        // Filter by search term
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function($sj) {
            return [
                $sj->no_surat_jalan,
                $sj->tanggal_gate_in ? \Carbon\Carbon::parse($sj->tanggal_gate_in)->format('d/m/Y H:i') : '-',
                $sj->tanggal_checkpoint ? \Carbon\Carbon::parse($sj->tanggal_checkpoint)->format('d/m/Y') : '-',
                $sj->no_kontainer ?? '-',
                $sj->supir ?? '-',
                $sj->tujuan_pengiriman ?? '-',
                $sj->catatan_checkpoint ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. Surat Jalan',
            'Tanggal Gate In',
            'Tanggal Keluar',
            'No. Kontainer',
            'Supir',
            'Tujuan',
            'Catatan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:G1')->getFont()->setBold(true);
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
