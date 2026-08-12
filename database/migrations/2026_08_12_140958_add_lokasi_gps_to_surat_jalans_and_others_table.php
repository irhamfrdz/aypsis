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
        $tables = [
            'surat_jalans', 
            'surat_jalan_bongkarans', 
            'surat_jalan_tarik_kosong_batams', 
            'surat_jalan_bongkaran_batams',
            'tanda_terima_batams'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->text('lokasi_gps')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'surat_jalans', 
            'surat_jalan_bongkarans', 
            'surat_jalan_tarik_kosong_batams', 
            'surat_jalan_bongkaran_batams',
            'tanda_terima_batams'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('lokasi_gps');
            });
        }
    }
};
