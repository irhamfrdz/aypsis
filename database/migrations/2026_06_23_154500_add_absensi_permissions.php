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
        // Insert absensi permissions
        DB::table('permissions')->insert([
            [
                'name' => 'absensi-view',
                'description' => 'Melihat menu kelola absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'kelola-absensi-view',
                'description' => 'Akses penuh menu kelola absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'absensi-create',
                'description' => 'Membuat data absensi baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'absensi-update',
                'description' => 'Mengubah data absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'absensi-delete',
                'description' => 'Menghapus data absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'absensi-rekap',
                'description' => 'Melihat rekapitulasi absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete absensi permissions
        DB::table('permissions')->whereIn('name', [
            'absensi-view',
            'kelola-absensi-view',
            'absensi-create',
            'absensi-update',
            'absensi-delete',
            'absensi-rekap',
        ])->delete();
    }
};
