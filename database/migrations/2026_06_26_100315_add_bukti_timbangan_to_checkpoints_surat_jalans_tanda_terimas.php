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
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->text('bukti_timbangan')->nullable();
        });

        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->text('bukti_timbangan')->nullable();
        });

        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->text('bukti_timbangan')->nullable();
        });

        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->text('bukti_timbangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn('bukti_timbangan');
        });

        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn('bukti_timbangan');
        });

        Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->dropColumn('bukti_timbangan');
        });

        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropColumn('bukti_timbangan');
        });
    }
};
