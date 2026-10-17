<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $permissions = [
            'master-penerima-bukti-potong-pph-view' => 'Melihat master penerima bukti potong PPh',
            'master-penerima-bukti-potong-pph-create' => 'Menambah penerima bukti potong PPh',
            'master-penerima-bukti-potong-pph-update' => 'Mengubah penerima bukti potong PPh',
            'master-penerima-bukti-potong-pph-delete' => 'Menghapus penerima bukti potong PPh',
        ];

        $rows = [];
        foreach ($permissions as $name => $description) {
            $rows[] = [
                'name' => $name,
                'description' => $description,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('permissions')->upsert($rows, ['name'], ['description', 'updated_at']);

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($permissions))
            ->pluck('id');
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        if (! $adminRoleId) {
            return;
        }

        $adminUserIds = DB::table('role_user')
            ->where('role_id', $adminRoleId)
            ->pluck('user_id');

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id' => $adminRoleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($adminUserIds as $userId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Permission juga dibuat oleh migration tabel sebelumnya. Jangan hapus
        // grant yang mungkin sudah diberikan secara manual saat rollback.
    }
};
