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
        DB::table('nomor_terakhir')->insert([
            'modul' => 'pembayaran_aktivitas_lainnya_pms',
            'nomor_terakhir' => 0,
            'keterangan' => 'Nomor pembayaran aktivitas lainnya dengan format PMS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('nomor_terakhir')->where('modul', 'pembayaran_aktivitas_lainnya_pms')->delete();
    }
};
