<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class VendorBengkelPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds to create vendor/bengkel permissions.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting vendor/bengkel permissions seeding...');

        // Get existing permissions to avoid duplicates
        $existingPermissions = Permission::pluck('name')->toArray();
        $newPermissions = [];

        // Vendor/Bengkel permissions
        $vendorBengkelPermissions = [
            ['name' => 'master-vendor-bengkel', 'description' => 'Akses Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.view', 'description' => 'Melihat Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.create', 'description' => 'Membuat Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.update', 'description' => 'Mengupdate Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.delete', 'description' => 'Menghapus Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.index', 'description' => 'Index Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.create', 'description' => 'Create Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.store', 'description' => 'Store Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.show', 'description' => 'Show Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.edit', 'description' => 'Edit Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.update', 'description' => 'Update Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.destroy', 'description' => 'Destroy Master Vendor/Bengkel'],
        ];

        foreach ($vendorBengkelPermissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $newPermissions[] = $permission;
            }
        }

        if (!empty($newPermissions)) {
            Permission::insert($newPermissions);
            $this->command->info('✅ Successfully created ' . count($newPermissions) . ' vendor/bengkel permissions');
        } else {
            $this->command->info('ℹ️  All vendor/bengkel permissions already exist');
        }

        $this->command->info('🎉 Vendor/Bengkel permissions seeding completed!');
    }
}
