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
            ['name' => 'pembayaran-pranota-ob-antar-gudang-view', 'description' => 'Melihat pembayaran pranota ob antar gudang'],
            ['name' => 'pembayaran-pranota-ob-antar-gudang-create', 'description' => 'Membuat pembayaran pranota ob antar gudang'],
            ['name' => 'pembayaran-pranota-ob-antar-gudang-edit', 'description' => 'Mengubah pembayaran pranota ob antar gudang'],
            ['name' => 'pembayaran-pranota-ob-antar-gudang-delete', 'description' => 'Menghapus pembayaran pranota ob antar gudang'],
            ['name' => 'pembayaran-pranota-ob-antar-gudang-print', 'description' => 'Mencetak pembayaran pranota ob antar gudang'],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Assign to Admin/Superadmin/Kiky users
        $adminUsers = DB::table('users')
            ->whereIn('username', ['admin', 'administrator', 'superadmin', 'kiky'])
            ->get();

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_column($permissions, 'name'))
            ->pluck('id');

        foreach ($adminUsers as $admin) {
            foreach ($permissionIds as $pId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => $admin->id, 'permission_id' => $pId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'pembayaran-pranota-ob-antar-gudang-view',
            'pembayaran-pranota-ob-antar-gudang-create',
            'pembayaran-pranota-ob-antar-gudang-edit',
            'pembayaran-pranota-ob-antar-gudang-delete',
            'pembayaran-pranota-ob-antar-gudang-print',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
