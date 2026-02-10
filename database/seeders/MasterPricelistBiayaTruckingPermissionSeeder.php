<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MasterPricelistBiayaTruckingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions for master pricelist biaya trucking
        $permissions = [
            'master-pricelist-biaya-trucking-view',
            'master-pricelist-biaya-trucking-create',
            'master-pricelist-biaya-trucking-update',
            'master-pricelist-biaya-trucking-delete',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->command->info('✓ All Master Pricelist Biaya Trucking permissions assigned to admin role');
        } else {
            $this->command->warn('⚠ Admin role not found. Permissions created but not assigned.');
        }

        $this->command->info('✓ Master Pricelist Biaya Trucking permissions seeded successfully');
    }
}
