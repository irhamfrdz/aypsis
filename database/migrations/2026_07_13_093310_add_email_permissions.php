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
            'email-view',
            'email-create',
            'email-delete',
            'email-settings',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

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
            'email-view',
            'email-create',
            'email-delete',
            'email-settings',
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
