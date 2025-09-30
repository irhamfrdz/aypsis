<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NomorTerakhirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\NomorTerakhir::create([
            'modul' => 'PRANOTA_SUPIR',
            'nomor_terakhir' => 150,
            'keterangan' => 'Nomor terakhir untuk pranota supir'
        ]);

        \App\Models\NomorTerakhir::create([
            'modul' => 'TAGIHAN_KONTAINER',
            'nomor_terakhir' => 75,
            'keterangan' => 'Nomor terakhir untuk tagihan kontainer'
        ]);

        \App\Models\NomorTerakhir::create([
            'modul' => 'PERBAIKAN_KONTAINER',
            'nomor_terakhir' => 25,
            'keterangan' => 'Nomor terakhir untuk perbaikan kontainer'
        ]);

        \App\Models\NomorTerakhir::create([
            'modul' => 'MEMO_PERMOHONAN',
            'nomor_terakhir' => 50,
            'keterangan' => 'Nomor terakhir untuk memo permohonan'
        ]);
    }
}
