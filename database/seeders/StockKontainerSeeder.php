<?php

namespace Database\Seeders;

use App\Models\StockKontainer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockKontainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stockKontainers = [
            [
                'nomor_kontainer' => 'ABCU1234567',
                'ukuran' => '20ft',
                'tipe_kontainer' => 'Dry Container',
                'status' => 'available',
                'lokasi' => 'Gudang A, Blok 1',
                'tanggal_masuk' => now()->subDays(30),
                'keterangan' => 'Kontainer baru dalam kondisi baik',
                'kondisi' => 'baik',
                'harga_sewa_per_hari' => 150000,
                'harga_sewa_per_bulan' => 4000000,
                'pemilik' => 'PT. Container Indonesia',
                'nomor_seri' => 'ABC123',
                'tahun_pembuatan' => 2020,
            ],
            [
                'nomor_kontainer' => 'DEFG9876543',
                'ukuran' => '40ft',
                'tipe_kontainer' => 'Reefer Container',
                'status' => 'rented',
                'lokasi' => 'Gudang B, Blok 2',
                'tanggal_masuk' => now()->subDays(60),
                'tanggal_keluar' => now()->addDays(30),
                'keterangan' => 'Sedang disewa untuk pengiriman makanan beku',
                'kondisi' => 'baik',
                'harga_sewa_per_hari' => 250000,
                'harga_sewa_per_bulan' => 7000000,
                'pemilik' => 'PT. Container Indonesia',
                'nomor_seri' => 'DEF456',
                'tahun_pembuatan' => 2019,
            ],
            [
                'nomor_kontainer' => 'HIJK5678901',
                'ukuran' => '40ft HC',
                'tipe_kontainer' => 'Dry Container',
                'status' => 'maintenance',
                'lokasi' => 'Workshop',
                'tanggal_masuk' => now()->subDays(15),
                'keterangan' => 'Sedang dalam perbaikan pintu',
                'kondisi' => 'rusak_ringan',
                'harga_sewa_per_hari' => 200000,
                'harga_sewa_per_bulan' => 5500000,
                'pemilik' => 'PT. Container Indonesia',
                'nomor_seri' => 'HIJ789',
                'tahun_pembuatan' => 2018,
            ],
            [
                'nomor_kontainer' => 'LMNO1357924',
                'ukuran' => '20ft',
                'tipe_kontainer' => 'Open Top',
                'status' => 'damaged',
                'lokasi' => 'Gudang C, Blok 3',
                'tanggal_masuk' => now()->subDays(90),
                'keterangan' => 'Atap rusak berat, perlu penggantian',
                'kondisi' => 'rusak_berat',
                'harga_sewa_per_hari' => 100000,
                'harga_sewa_per_bulan' => 2500000,
                'pemilik' => 'PT. Container Indonesia',
                'nomor_seri' => 'LMN012',
                'tahun_pembuatan' => 2017,
            ],
            [
                'nomor_kontainer' => 'PQRS2468135',
                'ukuran' => '45ft',
                'tipe_kontainer' => 'Dry Container',
                'status' => 'available',
                'lokasi' => 'Gudang A, Blok 4',
                'tanggal_masuk' => now()->subDays(7),
                'keterangan' => 'Kontainer baru siap disewa',
                'kondisi' => 'baik',
                'harga_sewa_per_hari' => 300000,
                'harga_sewa_per_bulan' => 8500000,
                'pemilik' => 'PT. Container Indonesia',
                'nomor_seri' => 'PQR345',
                'tahun_pembuatan' => 2022,
            ],
        ];

        foreach ($stockKontainers as $data) {
            StockKontainer::create($data);
        }
    }
}
