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
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin'])->pluck('id', 'name');

        if ($roles->isEmpty()) {
            return;
        }

        $permissions = DB::table('permissions')->whereIn('name', [
            'surat-jalan-kontainer-sewa-view',
            'surat-jalan-kontainer-sewa-create',
            'surat-jalan-kontainer-sewa-update',
            'surat-jalan-kontainer-sewa-delete',
            'surat-jalan-kontainer-sewa-print',
        ])->pluck('id');

        if ($permissions->isEmpty()) {
            return;
        }

        $data = [];
        foreach ($roles as $roleId) {
            foreach ($permissions as $permissionId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('permission_role')->insertOrIgnore($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin'])->pluck('id');
        $permissions = DB::table('permissions')->whereIn('name', [
            'surat-jalan-kontainer-sewa-view',
            'surat-jalan-kontainer-sewa-create',
            'surat-jalan-kontainer-sewa-update',
            'surat-jalan-kontainer-sewa-delete',
            'surat-jalan-kontainer-sewa-print',
        ])->pluck('id');

        if ($roles->isNotEmpty() && $permissions->isNotEmpty()) {
            DB::table('permission_role')
                ->whereIn('role_id', $roles)
                ->whereIn('permission_id', $permissions)
                ->delete();
        }
    }
};
