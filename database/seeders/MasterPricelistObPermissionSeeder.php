<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterPricelistObPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'master-pricelist-ob-view',
                'description' => 'Melihat daftar Pricelist OB'
            ],
            [
                'name' => 'master-pricelist-ob-create',
                'description' => 'Membuat Pricelist OB baru'
            ],
            [
                'name' => 'master-pricelist-ob-update',
                'description' => 'Mengupdate Pricelist OB'
            ],
            [
                'name' => 'master-pricelist-ob-delete',
                'description' => 'Menghapus Pricelist OB'
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }

        $this->command->info('Permissions untuk Master Pricelist OB berhasil ditambahkan');
    }
}
