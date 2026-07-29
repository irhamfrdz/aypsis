<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockAmprahanTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'No. Bukti',
            'Tanggal Beli (YYYY-MM-DD)',
            'Tipe Amprahan',
            'Type Barang (Kategori Master)',
            'Vendor/Toko',
            'Lokasi/Gudang',
            'Nama Barang (Spesifik)',
            'Jumlah',
            'Satuan',
            'Harga Satuan',
            'Keterangan'
        ];
    }

    public function array(): array
    {
        return [
            // Dummy example data to guide users
            [
                'BUKTI-001',
                date('Y-m-d'),
                'Perlengkapan',
                'OLI',
                'TOKO SEJAHTERA',
                'GUDANG UTAMA',
                'OLI MESIN SAE 40',
                '10',
                'Pcs',
                '50000',
                'Untuk stok cadangan'
            ]
        ];
    }
}
