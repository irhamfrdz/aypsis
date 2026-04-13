<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'pembayaran-pranota-lembur-view', 'description' => 'Melihat pembayaran pranota lembur'],
            ['name' => 'pembayaran-pranota-lembur-create', 'description' => 'Membuat pembayaran pranota lembur'],
            ['name' => 'pembayaran-pranota-lembur-edit', 'description' => 'Mengubah pembayaran pranota lembur'],
            ['name' => 'pembayaran-pranota-lembur-delete', 'description' => 'Menghapus pembayaran pranota lembur'],
            ['name' => 'pembayaran-pranota-lembur-approve', 'description' => 'Menyetujui pembayaran pranota lembur'],
            ['name' => 'pembayaran-pranota-lembur-print', 'description' => 'Mencetak pembayaran pranota lembur'],
            ['name' => 'pembayaran-pranota-lembur-export', 'description' => 'Mengexport pembayaran pranota lembur']
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Assign to Admin users
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
            'pembayaran-pranota-lembur-view',
            'pembayaran-pranota-lembur-create',
            'pembayaran-pranota-lembur-edit',
            'pembayaran-pranota-lembur-delete',
            'pembayaran-pranota-lembur-approve',
            'pembayaran-pranota-lembur-print',
            'pembayaran-pranota-lembur-export'
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
