<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterPricelistFreightPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'master-pricelist-freight-view',
                'description' => 'Melihat daftar Pricelist Freight'
            ],
            [
                'name' => 'master-pricelist-freight-create',
                'description' => 'Membuat Pricelist Freight baru'
            ],
            [
                'name' => 'master-pricelist-freight-update',
                'description' => 'Mengupdate Pricelist Freight'
            ],
            [
                'name' => 'master-pricelist-freight-delete',
                'description' => 'Menghapus Pricelist Freight'
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }

        $this->command->info('Permissions untuk Master Pricelist Freight berhasil ditambahkan');
    }
}
