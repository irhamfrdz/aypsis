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
            ['name' => 'surat-jalan-batam-view', 'description' => 'Melihat data Surat Jalan Batam'],
            ['name' => 'surat-jalan-batam-create', 'description' => 'Membuat Surat Jalan Batam'],
            ['name' => 'surat-jalan-batam-update', 'description' => 'Mengubah Surat Jalan Batam'],
            ['name' => 'surat-jalan-batam-delete', 'description' => 'Menghapus Surat Jalan Batam'],
            ['name' => 'surat-jalan-batam-export', 'description' => 'Export data Surat Jalan Batam']
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Assign to Administrator role if it exists
        $adminRole = DB::table('roles')->where('name', 'like', '%administrator%')->orWhere('name', 'like', '%admin%')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_column($permissions, 'name'))
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('permission_role')->updateOrInsert(
                    ['permission_id' => $pId, 'role_id' => $adminRole->id]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Handle rollback if necessary
        $permissionNames = [
            'surat-jalan-batam-view',
            'surat-jalan-batam-create',
            'surat-jalan-batam-update',
            'surat-jalan-batam-delete',
            'surat-jalan-batam-export'
        ];

        $adminRole = DB::table('roles')->where('name', 'like', '%administrator%')->orWhere('name', 'like', '%admin%')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissionNames)
                ->pluck('id');

            DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
