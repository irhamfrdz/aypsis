<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'pembayaran-pranota-uang-jalan-batam-view', 'description' => 'Melihat pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-create', 'description' => 'Membuat pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-edit', 'description' => 'Mengubah pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-delete', 'description' => 'Menghapus pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-approve', 'description' => 'Menyetujui pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-print', 'description' => 'Mencetak pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-export', 'description' => 'Mengexport pembayaran pranota uang jalan batam']
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

        // Assign to Admin role (direct user_permissions for administrator/admin/superadmin)
        $adminUsers = DB::table('users')
            ->whereIn('username', ['admin', 'administrator', 'superadmin'])
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
            'pembayaran-pranota-uang-jalan-batam-view',
            'pembayaran-pranota-uang-jalan-batam-create',
            'pembayaran-pranota-uang-jalan-batam-edit',
            'pembayaran-pranota-uang-jalan-batam-delete',
            'pembayaran-pranota-uang-jalan-batam-approve',
            'pembayaran-pranota-uang-jalan-batam-print',
            'pembayaran-pranota-uang-jalan-batam-export'
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
