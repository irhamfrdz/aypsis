<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class NomorTerakhirPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Master Nomor Terakhir permissions
            [
                'name' => 'master-nomor-terakhir',
                'description' => 'Manajemen Nomor Terakhir'
            ],
            [
                'name' => 'master-nomor-terakhir-view',
                'description' => 'Melihat data nomor terakhir'
            ],
            [
                'name' => 'master-nomor-terakhir-create',
                'description' => 'Membuat nomor terakhir baru'
            ],
            [
                'name' => 'master-nomor-terakhir-store',
                'description' => 'Menyimpan nomor terakhir baru'
            ],
            [
                'name' => 'master-nomor-terakhir-show',
                'description' => 'Melihat detail nomor terakhir'
            ],
            [
                'name' => 'master-nomor-terakhir-edit',
                'description' => 'Mengedit nomor terakhir'
            ],
            [
                'name' => 'master-nomor-terakhir-update',
                'description' => 'Memperbarui nomor terakhir'
            ],
            [
                'name' => 'master-nomor-terakhir-delete',
                'description' => 'Menghapus nomor terakhir'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Nomor Terakhir permissions seeded successfully!');
    }
}
