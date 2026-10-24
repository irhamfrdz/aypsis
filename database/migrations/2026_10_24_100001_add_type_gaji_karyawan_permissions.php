<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['view' => 'Lihat', 'create' => 'Tambah', 'update' => 'Ubah', 'delete' => 'Hapus'] as $action => $label) {
            DB::table('permissions')->updateOrInsert(['name' => 'master-type-gaji-karyawan-'.$action], [
                'description' => $label.' Master Type Gaji Karyawan', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        if (! Schema::hasTable('roles')) {
            return;
        }
        $roleId = DB::table('roles')->where('name', 'admin')->value('id');
        if (! $roleId) {
            return;
        }
        $ids = DB::table('permissions')->where('name', 'like', 'master-type-gaji-karyawan-%')->pluck('id');
        if (Schema::hasTable('permission_role')) {
            foreach ($ids as $id) {
                DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $id]);
            }
        }
        if (Schema::hasTable('role_user') && Schema::hasTable('user_permissions')) {
            foreach (DB::table('role_user')->where('role_id', $roleId)->pluck('user_id') as $userId) {
                foreach ($ids as $id) {
                    DB::table('user_permissions')->insertOrIgnore(['user_id' => $userId, 'permission_id' => $id]);
                }
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->where('name', 'like', 'master-type-gaji-karyawan-%')->pluck('id');
        foreach (['permission_role', 'user_permissions'] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->whereIn('permission_id', $ids)->delete();
            }
        }
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};
