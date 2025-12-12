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
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->string('lanjut_muat')->default('tidak')->after('tanggal_surat_jalan');
            $table->string('nomor_sj_sebelumnya')->nullable()->after('lanjut_muat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->dropColumn(['lanjut_muat', 'nomor_sj_sebelumnya']);
        });
    }
};
