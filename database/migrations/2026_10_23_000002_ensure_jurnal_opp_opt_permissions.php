<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'jurnal-opp-opt-view' => 'Melihat Jurnal OPT/OPP',
            'jurnal-opp-opt-create' => 'Menambah transaksi Jurnal OPT/OPP',
            'jurnal-opp-opt-update' => 'Mengubah transaksi Jurnal OPT/OPP',
            'jurnal-opp-opt-delete' => 'Menghapus transaksi Jurnal OPT/OPP',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($permissions))
            ->pluck('id');

        if (! Schema::hasTable('roles') || ! Schema::hasTable('permission_role')) {
            return;
        }

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        if (! $adminRoleId) {
            return;
        }

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }

        if (! Schema::hasTable('role_user') || ! Schema::hasTable('user_permissions')) {
            return;
        }

        $adminUserIds = DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id');
        foreach ($adminUserIds as $userId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Permission ini juga merupakan bagian dari migration pembuatan modul
        // Jurnal OPT/OPP, sehingga rollback migration penjamin ini tidak boleh
        // menghapus permission yang masih dibutuhkan oleh modul tersebut.
    }
};
