<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'master-sertifikat-kapal-view',
            'master-sertifikat-kapal-create',
            'master-sertifikat-kapal-update',
            'master-sertifikat-kapal-delete',
        ];

        foreach ($permissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName]);
            
            // Assign to admin role
            $role = Role::where('name', 'admin')->first();
            if ($role) {
                // Check if already attached
                if (!$role->permissions()->where('permissions.id', $permission->id)->exists()) {
                    $role->permissions()->attach($permission->id);
                }
            }
        }
    }

    public function down(): void
    {
        $permissions = [
            'master-sertifikat-kapal-view',
            'master-sertifikat-kapal-create',
            'master-sertifikat-kapal-update',
            'master-sertifikat-kapal-delete',
        ];

        // Detach from roles first to be safe
        $permissionsIds = Permission::whereIn('name', $permissions)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $permissionsIds)->delete();
        
        Permission::whereIn('name', $permissions)->delete();
    }
};
