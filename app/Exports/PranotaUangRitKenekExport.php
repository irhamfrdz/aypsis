<?php

namespace App\Exports;

use App\Models\PranotaUangRitKenek;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PranotaUangRitKenekExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PranotaUangRitKenek::with(['creator']);

        // Filter by search
        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_pranota', 'like', "%{$search}%")
                    ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('kenek_nama', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filter by date range
        if (! empty($this->filters['start_date']) && ! empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        $rows = $query->orderBy('created_at', 'desc')->get()->map(function ($p) {
            return [
                $p->no_pranota,
                $p->tanggal ? $p->tanggal->format('d/m/Y') : '-',
                $p->no_surat_jalan,
                $p->kenek_nama,
                $p->uang_rit_kenek,
                $p->total_hutang,
                $p->total_tabungan,
                $p->total_bpjs,
                $p->grand_total_bersih,
                ucfirst($p->status),
                $p->creator->name ?? 'System',
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nomor Pranota',
            'Tanggal',
            'No. Surat Jalan',
            'Kenek',
            'Uang Rit Kenek',
            'Potongan Hutang',
            'Potongan Tabungan',
            'Potongan BPJS',
            'Grand Total Bersih',
            'Status',
            'Created By',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
