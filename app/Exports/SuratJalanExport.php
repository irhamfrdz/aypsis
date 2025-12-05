<?php

namespace App\Exports;

use App\Models\SuratJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SuratJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $suratJalanIds;

    public function __construct(array $filters = [], array $suratJalanIds = [])
    {
        $this->filters = $filters;
        $this->suratJalanIds = $suratJalanIds;
    }

    public function collection()
    {
        if (!empty($this->suratJalanIds)) {
            $query = SuratJalan::with(['order', 'pranotaUangRit'])->whereIn('id', $this->suratJalanIds);
        } else {
            $query = SuratJalan::with(['order', 'pranotaUangRit'])->orderBy('created_at', 'desc');

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('pengirim', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('jenis_barang', 'like', "%{$search}%")
                      ->orWhere('tipe_kontainer', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%");
                });
            }

            if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
                $query->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['status_pembayaran']) && $this->filters['status_pembayaran'] !== 'all') {
                $query->where('status_pembayaran', $this->filters['status_pembayaran']);
            }

            if (!empty($this->filters['tipe_kontainer'])) {
                $query->where('tipe_kontainer', $this->filters['tipe_kontainer']);
            }

            if (!empty($this->filters['start_date'])) {
                $query->whereDate('tanggal_surat_jalan', '>=', $this->filters['start_date']);
            }

            if (!empty($this->filters['end_date'])) {
                $query->whereDate('tanggal_surat_jalan', '<=', $this->filters['end_date']);
            }
        }

        $rows = $query->get()->map(function($s) {
            return [
                $s->no_surat_jalan,
                $s->tanggal_surat_jalan ? (is_string($s->tanggal_surat_jalan) ? \Carbon\Carbon::parse($s->tanggal_surat_jalan)->format('d/m/Y') : $s->tanggal_surat_jalan->format('d/m/Y')) : '-',
                $s->order ? $s->order->nomor_order : '-',
                $s->pengirim,
                $s->tujuanPengambilanRelation->nama ?? $s->tujuan_pengambilan ?? '-',
                $s->tujuanPengirimanRelation->nama ?? $s->tujuan_pengiriman ?? '-',
                $s->jenis_barang,
                $s->tipe_kontainer,
                $s->no_kontainer,
                $s->supir,
                $s->status,
                $s->status_pembayaran,
                $s->pranota_uang_rit_count ?? 0
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No. Surat Jalan',
            'Tanggal',
            'Nomor Order',
            'Pengirim',
            'Tujuan Ambil',
            'Tujuan Kirim',
            'Barang',
            'Tipe Kontainer',
            'No. Kontainer',
            'Supir',
            'Status',
            'Status Pembayaran',
            'Jumlah Rit'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:M1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
