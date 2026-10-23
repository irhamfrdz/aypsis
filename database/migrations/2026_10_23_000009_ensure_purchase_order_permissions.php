<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'purchase-order-view' => 'Melihat Purchase Order',
            'purchase-order-create' => 'Menambah Purchase Order',
            'purchase-order-update' => 'Mengubah Purchase Order',
            'purchase-order-delete' => 'Menghapus Purchase Order',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('permission_role')) {
            return;
        }

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        if (! $adminRoleId) {
            return;
        }

        $permissionIds = DB::table('permissions')->whereIn('name', array_keys($permissions))->pluck('id');
        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ]);
        }

        if (! Schema::hasTable('role_user') || ! Schema::hasTable('user_permissions')) {
            return;
        }

        foreach (DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id') as $userId) {
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
        // Izin ini juga dibuat oleh migrasi tabel Purchase Order.
    }
};
