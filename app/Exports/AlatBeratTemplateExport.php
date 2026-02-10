<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlatBeratTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'kode_alat',
            'nama',
            'jenis',
            'merk',
            'tipe',
            'nomor_seri',
            'lokasi',
            'status',
            'keterangan',
        ];
    }

    public function array(): array
    {
        return [
            [
                '(Kosongkan = Auto)', // kode_alat example
                'Contoh Alat Berat', // nama
                'Excavator', // jenis
                'Komatsu', // merk
                'PC200-8', // tipe
                'SN12345678', // nomor_seri
                'Gudang Utama', // lokasi
                'active', // status (active/inactive/maintenance)
                'Kondisi baik', // keterangan
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }
}
