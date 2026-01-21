<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PricelistTkbmPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Pricelist TKBM
        $permissions = [
            'master-pricelist-tkbm-view',
            'master-pricelist-tkbm-create',
            'master-pricelist-tkbm-update',
            'master-pricelist-tkbm-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to admin role
        $role = Role::where('name', 'admin')->first();
        if ($role) {
            $role->givePermissionTo($permissions);
        }
    }
}
