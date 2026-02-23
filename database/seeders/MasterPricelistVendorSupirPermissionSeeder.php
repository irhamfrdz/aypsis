<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class MasterPricelistVendorSupirPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions for master pricelist vendor supir
        $permissions = [
            'master-pricelist-vendor-supir-view',
            'master-pricelist-vendor-supir-create',
            'master-pricelist-vendor-supir-update',
            'master-pricelist-vendor-supir-delete',
        ];

        // Create permissions if they don't exist
        $permissionIds = [];
        foreach ($permissions as $permissionName) {
            $perm = Permission::firstOrCreate([
                'name' => $permissionName,
            ], [
                'description' => ucwords(str_replace('-', ' ', $permissionName)),
            ]);
            $permissionIds[] = $perm->id;
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
            $this->command->info('✓ All Master Pricelist Vendor Supir permissions assigned to admin role');
        } else {
            $this->command->warn('⚠ Admin role not found. Permissions created but not assigned.');
        }

        $this->command->info('✓ Master Pricelist Vendor Supir permissions seeded successfully');
    }
}
