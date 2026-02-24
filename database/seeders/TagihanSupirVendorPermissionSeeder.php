<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class TagihanSupirVendorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tagihan Supir Vendor permissions
        $permissions = [
            [
                'name' => 'tagihan-supir-vendor-view',
                'description' => 'Melihat daftar tagihan supir vendor'
            ],
            [
                'name' => 'tagihan-supir-vendor-create',
                'description' => 'Menambah tagihan supir vendor'
            ],
            [
                'name' => 'tagihan-supir-vendor-update',
                'description' => 'Mengubah data tagihan supir vendor'
            ],
            [
                'name' => 'tagihan-supir-vendor-delete',
                'description' => 'Menghapus tagihan supir vendor'
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        $this->command->info('Tagihan Supir Vendor permissions created successfully!');
    }
}
