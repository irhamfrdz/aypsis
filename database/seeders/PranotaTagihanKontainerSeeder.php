<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\PranotaTagihanKontainer;
use Illuminate\Support\Facades\DB;

class PranotaTagihanKontainerSeeder extends Seeder
{
    public function run()
    {
        // Ensure permission exists
        Permission::firstOrCreate(['name' => 'master-pranota-tagihan-kontainer'], ['description' => 'Akses Pranota Tagihan Kontainer']);

        // Insert a couple of sample pranota records
        DB::table('pranota_tagihan_kontainers')->insertOrIgnore([
            ['nomor' => 'PTK-0001', 'tanggal' => now()->toDateString(), 'periode' => date('Y-m'), 'vendor' => 'AYP', 'keterangan' => 'Sample pranota 1', 'total' => 150000.00, 'created_at' => now(), 'updated_at' => now()],
            ['nomor' => 'PTK-0002', 'tanggal' => now()->toDateString(), 'periode' => date('Y-m'), 'vendor' => 'ZONA', 'keterangan' => 'Sample pranota 2', 'total' => 250000.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
