<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\SuratJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TandaTerimaFilteredExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $mode;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->mode = $filters['mode'] ?? '';
    }

    public function collection()
    {
        // If mode is 'missing', export SuratJalan rows matching filters
        if ($this->mode === 'missing') {
            $query = SuratJalan::query();

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%")
                      ->orWhere('no_kontainer', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%");
                });
            }

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            // Only missing tanda terima
            $query->whereDoesntHave('tandaTerima');

            $rows = $query->with('order.pengirim')->orderBy('created_at', 'desc')->get()->map(function($s) {
                return [
                    $s->no_surat_jalan,
                    $s->tanggal_surat_jalan ? $s->tanggal_surat_jalan->format('d/M/Y') : '-',
                    $s->no_kontainer,
                    $s->supir,
                    $s->no_plat,
                    $s->kegiatan,
                    optional($s->order->pengirim)->nama_pengirim ?? '-',
                ];
            });

            return $rows;
        }

        // Otherwise export TandaTerima rows
        $query = TandaTerima::with(['suratJalan.order.pengirim']);
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('estimasi_nama_kapal', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        $rows = $query->orderBy('created_at', 'desc')->get()->map(function($t) {
            return [
                $t->id,
                'TT-' . $t->id,
                $t->no_surat_jalan,
                $t->tanggal_checkpoint_supir ? $t->tanggal_checkpoint_supir->format('d/M/Y') : '-',
                $t->no_kontainer,
                $t->jenis_barang,
                $t->tujuan_pengiriman,
                $t->kegiatan,
                $t->status,
                optional($t->suratJalan->order->pengirim)->nama_pengirim ?? '-',
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        if ($this->mode === 'missing') {
            return ['No. Surat Jalan', 'Tanggal', 'No. Kontainer', 'Supir', 'No. Plat', 'Kegiatan', 'Pengirim'];
        }

        return ['ID', 'ID Tanda Terima', 'No. Surat Jalan', 'Tanggal', 'No. Kontainer', 'Jenis Barang', 'Tujuan', 'Kegiatan', 'Status', 'Pengirim'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
