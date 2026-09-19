<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'hrd-dashboard-view',
                'description' => 'Melihat Dashboard HRD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'hrd-dashboard-export',
                'description' => 'Export Rekap Data Absensi Dashboard HRD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore($permission);
        }

        // Assign to admin role
        if (Schema::hasTable('roles') && Schema::hasTable('permission_role')) {
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
        }

        // Give permissions to admin (user_id = 1) and users in HRD department
        if (Schema::hasTable('user_permissions')) {
            $permRecords = DB::table('permissions')->whereIn('name', ['hrd-dashboard-view', 'hrd-dashboard-export'])->get();

            // 1. Admin (user_id = 1)
            if (DB::table('users')->where('id', 1)->exists()) {
                foreach ($permRecords as $perm) {
                    DB::table('user_permissions')->insertOrIgnore([
                        'user_id' => 1,
                        'permission_id' => $perm->id,
                    ]);
                }
            }

            // 2. Existing HRD users & users with kelola-absensi-view / absensi-view
            $hrdUserIds = DB::table('users')
                ->join('karyawans', 'users.karyawan_id', '=', 'karyawans.id')
                ->where(function ($q) {
                    $q->whereRaw('UPPER(karyawans.divisi) = ?', ['HRD'])
                      ->orWhereRaw('UPPER(karyawans.pekerjaan) = ?', ['HRD']);
                })
                ->pluck('users.id');

            foreach ($hrdUserIds as $userId) {
                foreach ($permRecords as $perm) {
                    DB::table('user_permissions')->insertOrIgnore([
                        'user_id' => $userId,
                        'permission_id' => $perm->id,
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
            'hrd-dashboard-view',
            'hrd-dashboard-export',
        ];

        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');

        if (Schema::hasTable('permission_role')) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        }

        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        }

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
