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
            ['name' => 'pranota-puml-view', 'description' => 'View Riwayat Pranota PUML', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-puml-create', 'description' => 'Buat Pranota PUML', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-puml-update', 'description' => 'Edit Pranota PUML', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-puml-delete', 'description' => 'Hapus Pranota PUML', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore($permission);
        }

        // Assign to admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permission) {
                $permissionRecord = DB::table('permissions')->where('name', $permission['name'])->first();
                if ($permissionRecord) {
                    DB::table('permission_role')->insertOrIgnore([
                        'permission_id' => $permissionRecord->id,
                        'role_id' => $adminRole->id,
                    ]);
                }
            }
        }

        // Copy view permission to users who currently have pranota-uang-makan-view or payroll-view
        $viewPerm = DB::table('permissions')->where('name', 'pranota-puml-view')->first();
        if ($viewPerm) {
            $userIds = DB::table('user_permissions')
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->whereIn('permissions.name', ['pranota-uang-makan-view', 'payroll-view'])
                ->select('user_permissions.user_id')
                ->distinct()
                ->pluck('user_id');

            foreach ($userIds as $userId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id' => $userId,
                    'permission_id' => $viewPerm->id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'pranota-puml-view',
            'pranota-puml-create',
            'pranota-puml-update',
            'pranota-puml-delete',
        ];

        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
