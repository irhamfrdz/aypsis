<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TujuanSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $data = [
            [
                'deskripsi' => 'Area Kapuk',
                'uang_jalan' => 900000,
                'cabang' => 'JKT',
                'wilayah' => 'Dalam Kota',
                'rute' => 'Kapuk',
                'uang_jalan_20' => 900000,
                'ongkos_truk_20' => 350000,
                'uang_jalan_40' => 1100000,
                'ongkos_truk_40' => 375000,
                'antar_20' => 0,
                'antar_40' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'deskripsi' => 'Area Cipinang',
                'uang_jalan' => 1100000,
                'cabang' => 'JKT',
                'wilayah' => 'Cipinang',
                'rute' => 'Cipinang',
                'uang_jalan_20' => 1100000,
                'ongkos_truk_20' => 375000,
                'uang_jalan_40' => 0,
                'ongkos_truk_40' => 0,
                'antar_20' => 0,
                'antar_40' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('tujuans')->insert($data);
    }
}
