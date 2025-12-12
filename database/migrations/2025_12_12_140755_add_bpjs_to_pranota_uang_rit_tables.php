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
        // Tambah kolom BPJS ke tabel pranota_uang_rit_supir_details
        Schema::table('pranota_uang_rit_supir_details', function (Blueprint $table) {
            $table->decimal('bpjs', 15, 2)->default(0)->after('tabungan');
        });

        // Tambah kolom total_bpjs ke tabel pranota_uang_rits
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->decimal('total_bpjs', 15, 2)->default(0)->after('total_tabungan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom BPJS dari pranota_uang_rit_supir_details
        Schema::table('pranota_uang_rit_supir_details', function (Blueprint $table) {
            $table->dropColumn('bpjs');
        });

        // Hapus kolom total_bpjs dari pranota_uang_rits
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->dropColumn('total_bpjs');
        });
    }
};
