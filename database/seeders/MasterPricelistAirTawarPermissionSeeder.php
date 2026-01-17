<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MasterPricelistAirTawarPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions for master pricelist air tawar
        $permissions = [
            'master-pricelist-air-tawar-view',
            'master-pricelist-air-tawar-create',
            'master-pricelist-air-tawar-update',
            'master-pricelist-air-tawar-delete',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->command->info('✓ All Master Pricelist Air Tawar permissions assigned to admin role');
        } else {
            $this->command->warn('⚠ Admin role not found. Permissions created but not assigned.');
        }

        $this->command->info('✓ Master Pricelist Air Tawar permissions seeded successfully');
    }
}
