<?php

namespace App\Exports;

use App\Models\DaftarTagihanKontainerSewa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DaftarTagihanKontainerSewaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DaftarTagihanKontainerSewa::query();

        // Exclude GROUP_SUMMARY records
        $query->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
              ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%');

        // Apply filters
        if (!empty($this->filters['vendor'])) {
            $query->where('vendor', $this->filters['vendor']);
        }

        if (!empty($this->filters['size'])) {
            $query->where('size', $this->filters['size']);
        }

        if (!empty($this->filters['periode'])) {
            $query->where('periode', $this->filters['periode']);
        }

        if (!empty($this->filters['status'])) {
            $status = $this->filters['status'];
            if ($status === 'ongoing') {
                $query->whereNull('tanggal_akhir');
            } elseif ($status === 'selesai') {
                $query->whereNotNull('tanggal_akhir');
            }
        }

        if (!empty($this->filters['status_pranota'])) {
            $statusPranota = $this->filters['status_pranota'];
            if ($statusPranota === 'null' || $statusPranota === 'belum_pranota') {
                $query->whereNull('status_pranota');
            } elseif ($statusPranota === 'sudah_pranota') {
                $query->whereNotNull('status_pranota');
            } else {
                $query->where('status_pranota', $statusPranota);
            }
        }

        // Apply search if provided
        if (!empty($this->filters['q'])) {
            $searchTerm = $this->filters['q'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('group', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $query->orderBy('nomor_kontainer')->orderBy('periode');

        $tagihans = $query->get();

        return $tagihans->map(function ($tagihan) {
            return [
                $tagihan->group ?? '',
                $tagihan->vendor ?? '',
                $tagihan->nomor_kontainer ?? '',
                $tagihan->size ?? '',
                $tagihan->tanggal_awal ? Carbon::parse($tagihan->tanggal_awal)->format('d-m-Y') : '',
                $tagihan->tanggal_akhir ? Carbon::parse($tagihan->tanggal_akhir)->format('d-m-Y') : '',
                $tagihan->periode ?? '',
                $tagihan->masa ?? '',
                $tagihan->tarif ?? '',
                $tagihan->status ?? '',
                $tagihan->dpp ?? 0,
                $tagihan->adjustment ?? 0,
                $tagihan->dpp_nilai_lain ?? 0,
                $tagihan->ppn ?? 0,
                $tagihan->pph ?? 0,
                $tagihan->grand_total ?? 0,
                $tagihan->status_pranota ?? '',
                $tagihan->pranota_id ?? ''
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Group',
            'Vendor',
            'Nomor Kontainer',
            'Size',
            'Tanggal Awal',
            'Tanggal Akhir',
            'Periode',
            'Masa',
            'Tarif',
            'Status',
            'DPP',
            'Adjustment',
            'DPP Nilai Lain',
            'PPN',
            'PPH',
            'Grand Total',
            'Status Pranota',
            'Pranota ID'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
