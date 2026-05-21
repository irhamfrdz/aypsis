<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'master-alat-berat-view',
            'master-alat-berat-create',
            'master-alat-berat-update',
            'master-alat-berat-delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => 'Access to '.$name]
            );
        }

        // Assign to Admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-alat-berat-view',
            'master-alat-berat-create',
            'master-alat-berat-update',
            'master-alat-berat-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
