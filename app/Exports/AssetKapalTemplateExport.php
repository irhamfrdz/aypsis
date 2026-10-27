<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetKapalTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'kode_asset',
            'nama_asset',
            'nama_kapal',
            'lokasi_asset',
            'kategori',
            'spesifikasi',
            'kuantitas',
            'satuan',
            'tanggal_beli',
            'tanggal_kadaluarsa',
            'nama_merk',
            'nomor_seri',
            'kondisi',
            'status',
            'vendor',
            'nomor_faktur',
            'keterangan',
        ];
    }

    public function array(): array
    {
        return [[
            '(Kosongkan = Auto)',
            'Contoh nama aset kapal',
            'ISI_NAMA_KAPAL_SESUAI_MASTER',
            'DECK',
            'Perlengkapan',
            'Isi spesifikasi aset',
            1,
            'Unit',
            '2026-01-15',
            '2027-01-15',
            'Nama merk',
            '',
            'Bagus',
            'Tersedia',
            'Nama vendor',
            'NOMOR-INVOICE',
            'Keterangan tambahan',
        ]];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'],
                ],
            ],
        ];
    }
}
