<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'pranota-biaya-bensin-view',
            'pranota-biaya-bensin-create',
            'pranota-biaya-bensin-update',
            'pranota-biaya-bensin-delete',
            'pranota-biaya-bensin-print',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign to admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permission) {
                $permissionRecord = DB::table('permissions')->where('name', $permission)->first();
                if ($permissionRecord) {
                    DB::table('permission_role')->insertOrIgnore([
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
            'pranota-biaya-bensin-view',
            'pranota-biaya-bensin-create',
            'pranota-biaya-bensin-update',
            'pranota-biaya-bensin-delete',
            'pranota-biaya-bensin-print',
        ];

        DB::table('permission_role')
            ->whereIn('permission_id', function ($query) use ($permissions) {
                $query->select('id')
                    ->from('permissions')
                    ->whereIn('name', $permissions);
            })
            ->delete();

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
