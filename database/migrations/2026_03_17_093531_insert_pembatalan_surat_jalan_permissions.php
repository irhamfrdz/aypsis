<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('permissions')->insert([
            ['name' => 'pembatalan-surat-jalan-view', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pembatalan-surat-jalan-create', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pembatalan-surat-jalan-update', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pembatalan-surat-jalan-delete', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name', [
            'pembatalan-surat-jalan-view',
            'pembatalan-surat-jalan-create',
            'pembatalan-surat-jalan-update',
            'pembatalan-surat-jalan-delete'
        ])->delete();
    }
};
