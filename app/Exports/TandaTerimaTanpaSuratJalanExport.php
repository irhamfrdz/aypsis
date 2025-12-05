<?php

namespace App\Exports;

use App\Models\TandaTerimaTanpaSuratJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TandaTerimaTanpaSuratJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $ids;

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        $query = TandaTerimaTanpaSuratJalan::query();
        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        $rows = $query->with('term')
                      ->orderBy('created_at', 'desc')
                      ->get()
                      ->map(function ($t) {
                          return [
                              $t->no_tanda_terima ?? $t->nomor_tanda_terima ?? '',
                              $t->tanggal_tanda_terima ? $t->tanggal_tanda_terima->format('d/M/Y') : '',
                              $t->no_kontainer ?? '',
                              strtoupper($t->tipe_kontainer ?? ''),
                              $t->size_kontainer ?? '',
                              $t->no_seal ?? '',
                              $t->penerima ?? '',
                              $t->pengirim ?? '',
                              $t->jenis_barang ?? '',
                              $t->tujuan_pengambilan ?? '',
                              $t->tujuan_pengiriman ?? '',
                              $t->term ? ($t->term->nama_status ?? $t->term->name ?? '') : '',
                              number_format($t->getTotalVolumeAttribute() ?? 0, 6),
                              number_format($t->getTotalTonaseAttribute() ?? 0, 2),
                              ($t->tipe_kontainer === 'lcl' ? 'LCL Data' : 'Standard'),
                              $t->keterangan_barang ?? '',
                          ];
                      });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No. Tanda Terima',
            'Tanggal',
            'No. Kontainer',
            'Tipe',
            'Size Kontainer',
            'No. Seal',
            'Penerima',
            'Pengirim',
            'Jenis Barang',
            'Asal',
            'Tujuan',
            'Term',
            'Total Volume (m³)',
            'Total Berat (Ton)',
            'Sumber',
            'Keterangan'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:P1")->getFont()->setBold(true);
                $sheet->getStyle("A1:P{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
        ];
    }
}
