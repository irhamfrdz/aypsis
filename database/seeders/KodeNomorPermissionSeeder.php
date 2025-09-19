<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class KodeNomorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Master Kode Nomor permissions
            [
                'name' => 'master-kode-nomor',
                'description' => 'Manajemen Kode Nomor'
            ],
            [
                'name' => 'master-kode-nomor-view',
                'description' => 'Melihat data kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-create',
                'description' => 'Membuat kode nomor baru'
            ],
            [
                'name' => 'master-kode-nomor-store',
                'description' => 'Menyimpan kode nomor baru'
            ],
            [
                'name' => 'master-kode-nomor-show',
                'description' => 'Melihat detail kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-edit',
                'description' => 'Mengedit kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-update',
                'description' => 'Memperbarui kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-delete',
                'description' => 'Menghapus kode nomor'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Kode Nomor permissions seeded successfully!');
    }
}
