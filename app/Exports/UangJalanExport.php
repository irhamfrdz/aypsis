<?php

namespace App\Exports;

use App\Models\UangJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UangJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $uangJalanIds;

    public function __construct(array $filters = [], array $uangJalanIds = [])
    {
        $this->filters = $filters;
        $this->uangJalanIds = $uangJalanIds;
    }

    public function collection()
    {
        if (!empty($this->uangJalanIds)) {
            $query = UangJalan::with(['suratJalan.order.pengirim'])->whereIn('id', $this->uangJalanIds);
        } else {
            $query = UangJalan::with(['suratJalan.order.pengirim'])->orderBy('created_at', 'desc');

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                      ->orWhere('memo', 'like', "%{$search}%")
                      ->orWhereHas('suratJalan', function ($sjQ) use ($search) {
                          $sjQ->where('no_surat_jalan', 'like', "%{$search}%")
                              ->orWhere('supir', 'like', "%{$search}%")
                              ->orWhere('no_plat', 'like', "%{$search}%");
                      })
                      ->orWhereHas('suratJalan.order', function ($orderQ) use ($search) {
                          $orderQ->where('nomor_order', 'like', "%{$search}%")
                                 ->orWhereHas('pengirim', function ($pQ) use ($search) {
                                     $pQ->where('nama_pengirim', 'like', "%{$search}%");
                                 });
                      });
                });
            }

            if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
                $query->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['tanggal_dari'])) {
                $query->whereDate('tanggal_uang_jalan', '>=', $this->filters['tanggal_dari']);
            }

            if (!empty($this->filters['tanggal_sampai'])) {
                $query->whereDate('tanggal_uang_jalan', '<=', $this->filters['tanggal_sampai']);
            }
        }

        $rows = $query->get()->map(function($u) {
            return [
                $u->nomor_uang_jalan,
                $u->suratJalan ? ($u->suratJalan->no_surat_jalan ?? '-') : '-',
                $u->tanggal_uang_jalan ? (is_string($u->tanggal_uang_jalan) ? \Carbon\Carbon::parse($u->tanggal_uang_jalan)->format('d/m/Y') : $u->tanggal_uang_jalan->format('d/m/Y')) : '-',
                $u->suratJalan ? ($u->suratJalan->supir ?? '-') : '-',
                $u->jumlah_uang_jalan,
                $u->memo,
                $u->status,
                optional($u->suratJalan->order->pengirim)->nama_pengirim ?? '-' ,
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nomor Uang Jalan',
            'No. Surat Jalan',
            'Tanggal Uang Jalan',
            'Supir',
            'Jumlah',
            'Memo',
            'Status',
            'Pengirim'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
