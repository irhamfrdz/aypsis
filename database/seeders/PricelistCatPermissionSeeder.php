<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PricelistCatPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Pricelist CAT permissions
            [
                'name' => 'master-pricelist-cat-view',
                'description' => 'Melihat daftar pricelist CAT'
            ],
            [
                'name' => 'master-pricelist-cat-create',
                'description' => 'Membuat pricelist CAT baru'
            ],
            [
                'name' => 'master-pricelist-cat-update',
                'description' => 'Mengupdate pricelist CAT'
            ],
            [
                'name' => 'master-pricelist-cat-delete',
                'description' => 'Menghapus pricelist CAT'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Permissions untuk Pricelist CAT berhasil ditambahkan');
    }
}
