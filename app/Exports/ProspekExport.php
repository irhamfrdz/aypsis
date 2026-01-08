<?php

namespace App\Exports;

use App\Models\Prospek;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProspekExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $prospekIds;

    public function __construct(array $filters = [], array $prospekIds = [])
    {
        $this->filters = $filters;
        $this->prospekIds = $prospekIds;
    }

    public function collection()
    {
        // If prospekIds provided, export only those
        if (!empty($this->prospekIds)) {
            $query = Prospek::with(['suratJalan'])->whereIn('id', $this->prospekIds);
        } else {
            $query = Prospek::with(['suratJalan'])->orderBy('created_at', 'desc');

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }
            if (!empty($this->filters['tipe'])) {
                $query->where('tipe', $this->filters['tipe']);
            }
            if (!empty($this->filters['ukuran'])) {
                $query->where('ukuran', $this->filters['ukuran']);
            }
            if (!empty($this->filters['tujuan'])) {
                $query->where('tujuan_pengiriman', 'like', '%' . $this->filters['tujuan'] . '%');
            }
            if (!empty($this->filters['tanggal_dari']) && !empty($this->filters['tanggal_sampai'])) {
                $query->whereBetween('tanggal', [$this->filters['tanggal_dari'], $this->filters['tanggal_sampai']]);
            }
            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('nama_supir', 'like', "%{$search}%")
                      ->orWhere('barang', 'like', "%{$search}%")
                      ->orWhere('pt_pengirim', 'like', "%{$search}%")
                      ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_seal', 'like', "%{$search}%")
                      ->orWhere('tujuan_pengiriman', 'like', "%{$search}%")
                      ->orWhere('nama_kapal', 'like', "%{$search}%")
                      ->orWhere('no_surat_jalan', 'like', "%{$search}%");
                });
            }
        }

        $prospeks = $query->get();

        $rows = $prospeks->map(function($p, $index) {
            return [
                $index + 1, // Nomor urut
                $p->nama_supir ?? '-', // Nama supir
                $p->no_surat_jalan,
                $p->tanggal ? (is_string($p->tanggal) ? \Carbon\Carbon::parse($p->tanggal)->format('d/M/Y') : $p->tanggal->format('d/M/Y')) : '-',
                $p->barang,
                $p->pt_pengirim,
                $p->tipe,
                $p->ukuran,
                $p->nomor_kontainer,
                $p->no_seal,
                $p->tujuan_pengiriman ?? '-'
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Supir',
            'No. Surat Jalan',
            'Tanggal',
            'Barang',
            'PT/Pengirim',
            'Tipe',
            'Ukuran',
            'No. Kontainer',
            'No. Seal',
            'Tujuan'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
