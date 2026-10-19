<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PERMISSION = 'pranota-tagihan-lain-lain-batam-delete';

    public function up(): void
    {
        DB::table('permissions')->insertOrIgnore([
            'name' => self::PERMISSION,
            'description' => 'Hapus Pranota Tagihan Lain Lain Batam',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', self::PERMISSION)->value('id');
        if (! $permissionId) {
            return;
        }

        if (DB::getSchemaBuilder()->hasTable('user_permissions')) {
            DB::table('user_permissions')->where('permission_id', $permissionId)->delete();
        }

        DB::table('permissions')->where('id', $permissionId)->delete();
    }
};
