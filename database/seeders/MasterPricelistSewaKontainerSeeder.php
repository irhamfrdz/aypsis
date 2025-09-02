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
            [
                'vendor' => 'AYP',
                'tarif' => 'Standard',
                'ukuran_kontainer' => '20',
                'harga' => 22500.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'Auto seed AYP 20ft',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'AYP',
                'tarif' => 'Standard',
                'ukuran_kontainer' => '40',
                'harga' => 42000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'Auto seed AYP 40ft',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'ZONA',
                'tarif' => 'Standard',
                'ukuran_kontainer' => '40',
                'harga' => 42000.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'Auto seed ZONA 40ft',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'DPE',
                'tarif' => 'Standard',
                'ukuran_kontainer' => '20',
                'harga' => 22500.00,
                'tanggal_harga_awal' => now()->toDateString(),
                'keterangan' => 'Auto seed DPE 20ft',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('master_pricelist_sewa_kontainers')->insertOrIgnore($rows);
    }
}
