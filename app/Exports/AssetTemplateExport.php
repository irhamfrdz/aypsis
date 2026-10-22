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
            'nama_merk',
            'nomor_seri',
            'nik_pemegang',
            'nama_pemegang',
            'kategori',
            'tanggal_perolehan',
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
                'Dell',
                'SN-DELL-5420-001',
                'ISI_NIK_KARYAWAN',
                'ISI_NAMA_LENGKAP_KARYAWAN',
                'Elektronik & IT',
                '2024-01-15',
                'Baik',
                'Digunakan',
                'PT Dell Indonesia',
                'INV-DELL-0012',
                'Pengadaan laptop divisi IT',
            ],
            [
                '(Kosongkan = Auto)',
                'Forklift TCM 3 Ton Diesel',
                'TCM',
                'MESIN-TCM-0001',
                'ISI_NIK_KARYAWAN',
                'ISI_NAMA_LENGKAP_KARYAWAN',
                'Alat Berat',
                '2023-06-10',
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
