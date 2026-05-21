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
        // Define roles that should get these permissions
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin', 'operational'])->pluck('id', 'name');

        if ($roles->isEmpty()) {
            return;
        }

        // Define permissions to assign
        $permissions = DB::table('permissions')->whereIn('name', [
            'master-pricelist-tujuan-kontainer-sewa-view',
            'master-pricelist-tujuan-kontainer-sewa-create',
            'master-pricelist-tujuan-kontainer-sewa-update',
            'master-pricelist-tujuan-kontainer-sewa-delete',
        ])->pluck('id');

        if ($permissions->isEmpty()) {
            return;
        }

        $data = [];
        foreach ($roles as $roleName => $roleId) {
            foreach ($permissions as $permissionId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Use insertOrIgnore to avoid duplicates
        DB::table('permission_role')->insertOrIgnore($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin', 'operational'])->pluck('id');
        $permissions = DB::table('permissions')->whereIn('name', [
            'master-pricelist-tujuan-kontainer-sewa-view',
            'master-pricelist-tujuan-kontainer-sewa-create',
            'master-pricelist-tujuan-kontainer-sewa-update',
            'master-pricelist-tujuan-kontainer-sewa-delete',
        ])->pluck('id');

        if ($roles->isNotEmpty() && $permissions->isNotEmpty()) {
            DB::table('permission_role')
                ->whereIn('role_id', $roles)
                ->whereIn('permission_id', $permissions)
                ->delete();
        }
    }
};
