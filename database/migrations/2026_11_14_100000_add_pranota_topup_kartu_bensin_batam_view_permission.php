<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSION = 'pranota-topup-kartu-bensin-batam-view';

    public function up(): void
    {
        DB::table('permissions')->insertOrIgnore([
            'name' => self::PERMISSION,
            'description' => 'Lihat Pranota Top Up Kartu Bensin Batam',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permissionId = DB::table('permissions')->where('name', self::PERMISSION)->value('id');
        $masterPermissionId = DB::table('permissions')->where('name', 'master-kartu-bensin-batam-view')->value('id');

        if ($permissionId && Schema::hasTable('user_permissions')) {
            $userIds = $masterPermissionId
                ? DB::table('user_permissions')->where('permission_id', $masterPermissionId)->pluck('user_id')->all()
                : [];
            $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
            if ($adminRoleId && Schema::hasTable('role_user')) {
                $userIds = array_merge($userIds, DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id')->all());
            }
            foreach ($userIds as $userId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id' => $userId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($permissionId && Schema::hasTable('permission_role')) {
            $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
            if ($adminRoleId) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $adminRoleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', self::PERMISSION)->value('id');
        if (! $permissionId) {
            return;
        }

        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->where('permission_id', $permissionId)->delete();
        }
        if (Schema::hasTable('permission_role')) {
            DB::table('permission_role')->where('permission_id', $permissionId)->delete();
        }
        DB::table('permissions')->where('id', $permissionId)->delete();
    }
};
