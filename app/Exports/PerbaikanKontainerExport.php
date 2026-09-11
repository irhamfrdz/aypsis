<?php

namespace App\Exports;

use App\Models\PerbaikanKontainer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PerbaikanKontainerExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $query = PerbaikanKontainer::with(['bengkel', 'creator', 'updater'])
            ->orderBy('created_at', 'desc');

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_perbaikan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%");
            });
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['vendor_bengkel_id'])) {
            $query->where('vendor_bengkel_id', $this->filters['vendor_bengkel_id']);
        }

        if (! empty($this->filters['tanggal_masuk_start'])) {
            $query->whereDate('tanggal_masuk', '>=', $this->filters['tanggal_masuk_start']);
        }

        if (! empty($this->filters['tanggal_masuk_end'])) {
            $query->whereDate('tanggal_masuk', '<=', $this->filters['tanggal_masuk_end']);
        }

        $statusPranota = $this->filters['status_pranota'] ?? 'Belum';
        if ($statusPranota && $statusPranota !== 'all') {
            $query->where('status_pranota', $statusPranota);
        }

        return $query->get()->values()->map(function (PerbaikanKontainer $perbaikan, int $index) {
            $biayaPerbaikan = (float) $perbaikan->biaya_riil > 0
                ? $perbaikan->biaya_riil
                : $perbaikan->estimasi_biaya;

            return [
                $index + 1,
                $perbaikan->no_perbaikan,
                $perbaikan->no_kontainer,
                $perbaikan->ukuran,
                $perbaikan->tipe_kontainer,
                $perbaikan->tanggal_masuk?->format('d/m/Y'),
                $perbaikan->tanggal_keluar?->format('d/m/Y'),
                $perbaikan->bengkel?->nama_bengkel ?? '-',
                $perbaikan->keterangan_kerusakan ?? '-',
                $perbaikan->keterangan_perbaikan ?? '-',
                $perbaikan->estimasi_biaya,
                $perbaikan->biaya_riil,
                $biayaPerbaikan,
                $perbaikan->is_cat ? 'Ya' : 'Tidak',
                $perbaikan->is_cat ? ($perbaikan->jenis_cat === 'cat_full' ? 'Full' : 'Sebagian') : '-',
                $perbaikan->vendor_cat ?? '-',
                $perbaikan->biaya_cat,
                $biayaPerbaikan + (float) $perbaikan->biaya_cat,
                ucfirst($perbaikan->status),
                $perbaikan->status_pranota,
                $perbaikan->creator?->name ?? '-',
                $perbaikan->updater?->name ?? '-',
                $perbaikan->created_at?->format('d/m/Y H:i:s'),
                $perbaikan->updated_at?->format('d/m/Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'NO. PERBAIKAN',
            'NO. KONTAINER',
            'UKURAN',
            'TIPE KONTAINER',
            'TGL MASUK',
            'TGL SELESAI',
            'BENGKEL / VENDOR',
            'KETERANGAN KERUSAKAN',
            'KETERANGAN PERBAIKAN',
            'ESTIMASI BIAYA',
            'BIAYA RIIL',
            'BIAYA PERBAIKAN TERPAKAI',
            'ADA CAT',
            'JENIS CAT',
            'VENDOR CAT',
            'BIAYA CAT',
            'TOTAL BIAYA',
            'STATUS',
            'STATUS PRANOTA',
            'DIBUAT OLEH',
            'DIUBAH OLEH',
            'DIBUAT PADA',
            'DIUBAH PADA',
        ];
    }

    public function columnFormats(): array
    {
        return array_fill_keys(['K', 'L', 'M', 'Q', 'R'], '#,##0.00');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A1:{$highestColumn}1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9EAF7');
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setARGB('D9D9D9');
                $sheet->freezePane('A2');
            },
        ];
    }
}
