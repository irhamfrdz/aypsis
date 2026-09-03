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
            'master-rumus-bpjs-view',
            'master-rumus-bpjs-create',
            'master-rumus-bpjs-update',
            'master-rumus-bpjs-delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => 'Access to ' . $name]
            );
        }

        // Assign semua permission ke role Admin
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
            'master-rumus-bpjs-view',
            'master-rumus-bpjs-create',
            'master-rumus-bpjs-update',
            'master-rumus-bpjs-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
