<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricelistCatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $adminUserId = DB::table('users')->where('username', 'admin')->value('id') ?? 1;

        $pricelists = [
            [
                'vendor' => 'AYP Cat Service',
                'jenis_cat' => 'cat_sebagian',
                'ukuran_kontainer' => '20ft',
                'tarif' => 15000.00,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'AYP Cat Service',
                'jenis_cat' => 'cat_full',
                'ukuran_kontainer' => '20ft',
                'tarif' => 25000.00,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'Zona Container Painting',
                'jenis_cat' => 'cat_sebagian',
                'ukuran_kontainer' => '40ft',
                'tarif' => 14000.00,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'Zona Container Painting',
                'jenis_cat' => 'cat_full',
                'ukuran_kontainer' => '40ft',
                'tarif' => 23000.00,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'DPE Painting Service',
                'jenis_cat' => 'cat_sebagian',
                'ukuran_kontainer' => '40ft HC',
                'tarif' => 16000.00,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'vendor' => 'DPE Painting Service',
                'jenis_cat' => 'cat_full',
                'ukuran_kontainer' => '40ft HC',
                'tarif' => 26000.00,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($pricelists as $pricelist) {
            DB::table('pricelist_cat')->insert($pricelist);
        }

        $this->command->info('Data sample pricelist CAT berhasil ditambahkan');
    }
}
