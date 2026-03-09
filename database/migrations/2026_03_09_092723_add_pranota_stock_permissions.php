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
            ['name' => 'pranota-stock-view', 'description' => 'Akses Menu Riwayat Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-print', 'description' => 'Menu Cetak Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-delete', 'description' => 'Menu Hapus Riwayat Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'pranota-stock-view',
            'pranota-stock-print',
            'pranota-stock-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
