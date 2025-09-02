<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            ['id' => 1, 'name' => 'master-karyawan'],
            ['id' => 2, 'name' => 'master-user'],
            ['id' => 3, 'name' => 'master-kontainer'],
            ['id' => 4, 'name' => 'master-permohonan'],
            ['id' => 5, 'name' => 'permohonan-create'],
            ['id' => 6, 'name' => 'permohonan-view'],
            ['id' => 7, 'name' => 'permohonan-edit'],
            ['id' => 8, 'name' => 'permohonan-delete'],
            ['id' => 9, 'name' => 'master-tujuan'],
            ['id' => 10, 'name' => 'master-kegiatan'],
            ['id' => 11, 'name' => 'master-permission'],
            ['id' => 12, 'name' => 'master-mobil'],
            ['id' => 13, 'name' => 'master-pricelist-sewa-kontainer'],
            ['id' => 14, 'name' => 'master-pranota-supir'],
            ['id' => 15, 'name' => 'master-pranota'],
            ['id' => 16, 'name' => 'master-pranota-tagihan-kontainer'],
            ['id' => 17, 'name' => 'master-pembayaran-pranota-supir'],
        ]);
    }
}
