<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'master-wa-templates-view',
            'master-wa-templates-create',
            'master-wa-templates-update',
            'master-wa-templates-delete',
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign to admin role
        $adminRole = \Illuminate\Support\Facades\DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permission) {
                $permissionRecord = \Illuminate\Support\Facades\DB::table('permissions')->where('name', $permission)->first();
                if ($permissionRecord) {
                    \Illuminate\Support\Facades\DB::table('permission_role')->insertOrIgnore([
                        'role_id' => $adminRole->id,
                        'permission_id' => $permissionRecord->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-wa-templates-view',
            'master-wa-templates-create',
            'master-wa-templates-update',
            'master-wa-templates-delete',
        ];

        foreach ($permissions as $permission) {
            $permissionRecord = \Illuminate\Support\Facades\DB::table('permissions')->where('name', $permission)->first();
            if ($permissionRecord) {
                \Illuminate\Support\Facades\DB::table('permission_role')->where('permission_id', $permissionRecord->id)->delete();
                \Illuminate\Support\Facades\DB::table('permissions')->where('id', $permissionRecord->id)->delete();
            }
        }
    }
};
