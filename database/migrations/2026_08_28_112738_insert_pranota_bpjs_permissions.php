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
            'pranota-bpjs-view',
            'pranota-bpjs-create',
            'pranota-bpjs-update',
            'pranota-bpjs-delete',
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
                        'permission_id' => $permissionRecord->id,
                        'role_id' => $adminRole->id,
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
            'pranota-bpjs-view',
            'pranota-bpjs-create',
            'pranota-bpjs-update',
            'pranota-bpjs-delete',
        ];

        \Illuminate\Support\Facades\DB::table('permission_role')
            ->whereIn('permission_id', function ($query) use ($permissions) {
                $query->select('id')
                    ->from('permissions')
                    ->whereIn('name', $permissions);
            })
            ->delete();

        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
