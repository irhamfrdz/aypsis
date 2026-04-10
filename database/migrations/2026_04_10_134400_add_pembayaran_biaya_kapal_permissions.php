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
            ['name' => 'pembayaran-biaya-kapal-view', 'description' => 'Melihat daftar pembayaran biaya kapal'],
            ['name' => 'pembayaran-biaya-kapal-create', 'description' => 'Membuat pembayaran biaya kapal baru'],
            ['name' => 'pembayaran-biaya-kapal-edit', 'description' => 'Mengubah data pembayaran biaya kapal'],
            ['name' => 'pembayaran-biaya-kapal-delete', 'description' => 'Menghapus/membatalkan pembayaran biaya kapal'],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists to avoid duplicates
            $existing = DB::table('permissions')->where('name', $permission['name'])->first();
            
            if (!$existing) {
                $permissionId = DB::table('permissions')->insertGetId(array_merge($permission, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));

                // Assign to admin and super-admin
                $roles = DB::table('roles')->whereIn('name', ['admin', 'super-admin'])->pluck('id');
                foreach ($roles as $roleId) {
                    DB::table('permission_role')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
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
        $permissionNames = [
            'pembayaran-biaya-kapal-view',
            'pembayaran-biaya-kapal-create',
            'pembayaran-biaya-kapal-edit',
            'pembayaran-biaya-kapal-delete',
        ];

        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }
    }
};
