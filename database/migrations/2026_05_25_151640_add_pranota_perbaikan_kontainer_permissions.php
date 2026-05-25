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
            ['name' => 'pranota-perbaikan-kontainer-view', 'description' => 'Akses Menu Riwayat Pranota Perbaikan Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-perbaikan-kontainer-create', 'description' => 'Menu Buat Pranota Perbaikan Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-perbaikan-kontainer-update', 'description' => 'Menu Edit Pranota Perbaikan Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-perbaikan-kontainer-delete', 'description' => 'Menu Hapus Pranota Perbaikan Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-perbaikan-kontainer-print', 'description' => 'Menu Cetak Pranota Perbaikan Kontainer', 'created_at' => now(), 'updated_at' => now()],
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
            'pranota-perbaikan-kontainer-view',
            'pranota-perbaikan-kontainer-create',
            'pranota-perbaikan-kontainer-update',
            'pranota-perbaikan-kontainer-delete',
            'pranota-perbaikan-kontainer-print',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
