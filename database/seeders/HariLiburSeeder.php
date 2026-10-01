<?php

namespace Database\Seeders;

use App\Models\HariLibur;
use Illuminate\Database\Seeder;

class HariLiburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HariLibur::updateOrCreate(
            ['tanggal' => '2026-08-17'],
            ['keterangan' => 'Hari Kemerdekaan RI']
        );
    }
}
