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
        DB::table('permissions')->insert([
            [
                'name' => 'master-lokasi-absensi-view',
                'description' => 'Melihat daftar lokasi absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-lokasi-absensi-create',
                'description' => 'Membuat lokasi absensi baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-lokasi-absensi-update',
                'description' => 'Mengupdate data lokasi absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-lokasi-absensi-delete',
                'description' => 'Menghapus data lokasi absensi',
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
        DB::table('permissions')->whereIn('name', [
            'master-lokasi-absensi-view',
            'master-lokasi-absensi-create',
            'master-lokasi-absensi-update',
            'master-lokasi-absensi-delete',
        ])->delete();
    }
};
