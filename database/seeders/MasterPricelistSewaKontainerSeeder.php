<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPricelistSewaKontainerSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $rows = [
            // DPE Vendor
            [
                'vendor' => 'DPE',
                'tarif' => 'Harian',
                'ukuran_kontainer' => '20',
                'harga' => 25000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'DPE 20ft - Tarif Harian',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'DPE',
                'tarif' => 'Harian',
                'ukuran_kontainer' => '40',
                'harga' => 35000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'DPE 40ft - Tarif Harian',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // ZONA Vendor
            [
                'vendor' => 'ZONA',
                'tarif' => 'Harian',
                'ukuran_kontainer' => '20',
                'harga' => 20000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'ZONA 20ft - Tarif Harian',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'ZONA',
                'tarif' => 'Harian',
                'ukuran_kontainer' => '40',
                'harga' => 30000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'ZONA 40ft - Tarif Harian',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // AYP Vendor (existing data structure preserved)
            [
                'vendor' => 'AYP',
                'tarif' => 'Standard',
                'ukuran_kontainer' => '20',
                'harga' => 22500.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'AYP 20ft - Tarif Standard',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'AYP',
                'tarif' => 'Standard',
                'ukuran_kontainer' => '40',
                'harga' => 42000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'AYP 40ft - Tarif Standard',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('master_pricelist_sewa_kontainers')->insertOrIgnore($rows);
    }
}
