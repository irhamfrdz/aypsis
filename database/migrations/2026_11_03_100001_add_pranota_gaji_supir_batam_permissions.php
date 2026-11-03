<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        'pranota-gaji-supir-batam-view' => 'Lihat Pranota Gaji Supir Batam',
        'pranota-gaji-supir-batam-create' => 'Buat Pranota Gaji Supir Batam',
        'pranota-gaji-supir-batam-delete' => 'Hapus Pranota Gaji Supir Batam',
    ];

    public function up(): void
    {
        foreach (self::PERMISSIONS as $name => $description) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('permissions')->where('name', $name)->update([
                'description' => $description,
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('users') && Schema::hasTable('user_permissions')) {
            $adminIds = DB::table('users')->where('username', 'admin')->pluck('id');
            $permissionIds = DB::table('permissions')->whereIn('name', array_keys(self::PERMISSIONS))->pluck('id');
            foreach ($adminIds as $userId) {
                foreach ($permissionIds as $permissionId) {
                    DB::table('user_permissions')->insertOrIgnore([
                        'user_id' => $userId,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Keep permission rows and grants: they may already have existed before this migration.
    }
};
