<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'kode_asset',
            'nama_asset',
            'kategori',
            'tanggal_perolehan',
            'masa_manfaat_bulan',
            'nilai_residu',
            'kondisi',
            'status',
            'vendor',
            'nomor_faktur',
            'keterangan',
        ];
    }

    public function array(): array
    {
        return [
            [
                '(Kosongkan = Auto)',
                'Laptop Dell Latitude 5420',
                'Elektronik & IT',
                '2024-01-15',
                '48',
                '1000000',
                'Baik',
                'Digunakan',
                'PT Dell Indonesia',
                'INV-DELL-0012',
                'Pengadaan laptop divisi IT',
            ],
            [
                '(Kosongkan = Auto)',
                'Forklift TCM 3 Ton Diesel',
                'Alat Berat',
                '2023-06-10',
                '96',
                '20000000',
                'Baik',
                'Tersedia',
                'PT Heavyindo Perkasa',
                'INV-HP-2023-04',
                'Forklift operasional gudang',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
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
