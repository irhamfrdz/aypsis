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
        // Create Master Nama Stock Ban permissions
        $permissions = [
            'master-nama-stock-ban-view',
            'master-nama-stock-ban-create',
            'master-nama-stock-ban-update',
            'master-nama-stock-ban-delete',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get admin role ID and permission IDs to assign permissions
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permission) {
                $permissionRecord = DB::table('permissions')->where('name', $permission)->first();
                if ($permissionRecord) {
                    DB::table('permission_role')->insertOrIgnore([
                        'permission_id' => $permissionRecord->id,
                        'role_id' => $adminRole->id,
                        'created_at' => now(),
                        'updated_at' => now(),
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
            'master-nama-stock-ban-view',
            'master-nama-stock-ban-create',
            'master-nama-stock-ban-update',
            'master-nama-stock-ban-delete',
        ];

        // Remove role permissions first
        DB::table('permission_role')
          ->whereIn('permission_id', function($query) use ($permissions) {
              $query->select('id')
                    ->from('permissions')
                    ->whereIn('name', $permissions);
          })
          ->delete();

        // Remove permissions
        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
