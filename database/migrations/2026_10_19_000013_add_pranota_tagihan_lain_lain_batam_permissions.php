<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PERMISSIONS = [
        'pranota-tagihan-lain-lain-batam-view' => 'Lihat Pranota Tagihan Lain Lain Batam',
        'pranota-tagihan-lain-lain-batam-create' => 'Buat Pranota Tagihan Lain Lain Batam',
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
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->whereIn('name', array_keys(self::PERMISSIONS))->pluck('id');

        if (DB::getSchemaBuilder()->hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('permission_id', $ids)->delete();
        }

        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};
