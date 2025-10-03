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
                'tanggal_masuk' => now()->subDays(30),
                'keterangan' => 'Kontainer baru dalam kondisi baik',
                'nomor_seri' => 'ABC123',
                'tahun_pembuatan' => 2020,
            ],
            [
                'nomor_kontainer' => 'DEFG9876543',
                'ukuran' => '40ft',
                'tipe_kontainer' => 'Reefer Container',
                'status' => 'rented',
                'tanggal_masuk' => now()->subDays(60),
                'tanggal_keluar' => now()->addDays(30),
                'keterangan' => 'Sedang disewa untuk pengiriman makanan beku',
                'nomor_seri' => 'DEF456',
                'tahun_pembuatan' => 2019,
            ],
            [
                'nomor_kontainer' => 'HIJK5678901',
                'ukuran' => '40ft HC',
                'tipe_kontainer' => 'Dry Container',
                'status' => 'maintenance',
                'tanggal_masuk' => now()->subDays(15),
                'keterangan' => 'Sedang dalam perbaikan pintu',
                'nomor_seri' => 'HIJ789',
                'tahun_pembuatan' => 2018,
            ],
            [
                'nomor_kontainer' => 'LMNO1357924',
                'ukuran' => '20ft',
                'tipe_kontainer' => 'Open Top',
                'status' => 'damaged',
                'tanggal_masuk' => now()->subDays(90),
                'keterangan' => 'Atap rusak berat, perlu penggantian',
                'nomor_seri' => 'LMN012',
                'tahun_pembuatan' => 2017,
            ],
            [
                'nomor_kontainer' => 'PQRS2468135',
                'ukuran' => '45ft',
                'tipe_kontainer' => 'Dry Container',
                'status' => 'available',
                'tanggal_masuk' => now()->subDays(7),
                'keterangan' => 'Kontainer baru siap disewa',
                'nomor_seri' => 'PQR345',
                'tahun_pembuatan' => 2022,
            ],
        ];

        foreach ($stockKontainers as $data) {
            StockKontainer::updateOrCreate(
                ['nomor_kontainer' => $data['nomor_kontainer']],
                $data
            );
        }
    }
}
