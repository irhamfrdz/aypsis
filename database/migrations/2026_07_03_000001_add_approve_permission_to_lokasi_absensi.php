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
                'name' => 'master-lokasi-absensi-approve',
                'description' => 'Menyetujui pengaturan lokasi absensi',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'master-lokasi-absensi-approve')
            ->delete();
    }
};
