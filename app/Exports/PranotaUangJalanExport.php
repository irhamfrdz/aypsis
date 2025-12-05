<?php

namespace App\Exports;

use App\Models\PranotaUangJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PranotaUangJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $pranotaIds;

    public function __construct(array $filters = [], array $pranotaIds = [])
    {
        $this->filters = $filters;
        $this->pranotaIds = $pranotaIds;
    }

    public function collection()
    {
        if (!empty($this->pranotaIds)) {
            $query = PranotaUangJalan::with(['uangJalans'])->whereIn('id', $this->pranotaIds);
        } else {
            $query = PranotaUangJalan::with(['uangJalans'])->orderBy('created_at', 'desc');

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('nomor_pranota', 'like', "%{$search}%")
                      ->orWhereHas('uangJalans', function($sq) use ($search) {
                          $sq->where('nomor_uang_jalan', 'like', "%{$search}%");
                      });
                });
            }

            if (!empty($this->filters['status'])) {
                $query->where('status_pembayaran', $this->filters['status']);
            }
        }

        $rows = $query->get()->map(function($p) {
            return [
                $p->nomor_pranota,
                $p->tanggal_pranota ? (is_string($p->tanggal_pranota) ? \Carbon\Carbon::parse($p->tanggal_pranota)->format('d/m/Y') : $p->tanggal_pranota->format('d/m/Y')) : '-',
                $p->jumlah_uang_jalan,
                $p->total_amount,
                $p->status_pembayaran,
                $p->periode_tagihan,
                $p->createdBy->username ?? 'System'
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nomor Pranota',
            'Tanggal Pranota',
            'Jumlah Uang Jalan',
            'Total Amount',
            'Status Pembayaran',
            'Periode Tagihan',
            'Created By'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
