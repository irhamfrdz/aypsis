<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AsuransiTandaTerimaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $receipts;

    public function __construct(Collection $receipts)
    {
        $this->receipts = $receipts;
    }

    public function collection()
    {
        return $this->receipts;
    }

    public function headings(): array
    {
        return [
            'Tipe Dokumen',
            'Nomor Dokumen',
            'Tanggal',
            'No. Kontainer',
            'Nama Barang',
            'Kuantitas',
            'Satuan',
            'Pengirim',
            'Penerima',
            'Status Asuransi',
            'Nomor Polis',
            'Vendor Asuransi',
            'Kapal',
            'Voyage'
        ];
    }

    public function map($item): array
    {
        $statusAsuransi = $item->insurance ? 'Terproteksi' : 'Belum Diasuransikan';
        
        $namaBarang = $item->nama_barang;
        if ($item->type == 'tt' && is_string($namaBarang) && (str_starts_with($namaBarang, '[') || str_starts_with($namaBarang, '{'))) {
            $decoded = json_decode($namaBarang, true);
            $namaBarang = is_array($decoded) ? implode(', ', $decoded) : $namaBarang;
        }

        return [
            $this->getTipeLabel($item->type),
            $item->number,
            Carbon::parse($item->date)->format('d/m/Y'),
            $item->no_kontainer ?: '-',
            $namaBarang ?: '-',
            $item->kuantitas ?: '-',
            $item->satuan ?: '-',
            $item->pengirim ?: '-',
            $item->penerima ?: '-',
            $statusAsuransi,
            $item->insurance ? $item->insurance->nomor_polis : '-',
            $item->insurance ? optional($item->insurance->vendorAsuransi)->nama_asuransi : '-',
            $item->insurance ? $item->insurance->nama_kapal : '-',
            $item->insurance ? $item->insurance->nomor_voyage : '-',
        ];
    }

    private function getTipeLabel($type)
    {
        switch ($type) {
            case 'tt': return 'Tanda Terima';
            case 'tttsj': return 'TT Tanpa SJ';
            case 'lcl': return 'Tanda Terima LCL';
            default: return $type;
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
