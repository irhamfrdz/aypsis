<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class TipeAkunPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Master Tipe Akun permissions
        $permissions = [
            [
                'name' => 'master-tipe-akun-view',
                'description' => 'Melihat daftar tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-create',
                'description' => 'Menambah tipe akun baru'
            ],
            [
                'name' => 'master-tipe-akun-update',
                'description' => 'Mengedit tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-delete',
                'description' => 'Menghapus tipe akun'
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        $this->command->info('Master Tipe Akun permissions created successfully!');
    }
}
