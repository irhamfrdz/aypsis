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
        Schema::table('pranota_uang_rit_batam_items', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_jalan_batam_id')->nullable()->change();
            $table->unsignedBigInteger('surat_jalan_bongkaran_batam_id')->nullable()->after('surat_jalan_batam_id');
            $table->unsignedBigInteger('surat_jalan_tarik_kosong_batam_id')->nullable()->after('surat_jalan_bongkaran_batam_id');

            $table->foreign('surat_jalan_bongkaran_batam_id', 'fk_sj_bongkaran_batam_id')->references('id')->on('surat_jalan_bongkaran_batams')->onDelete('cascade');
            $table->foreign('surat_jalan_tarik_kosong_batam_id', 'fk_sj_tarik_kosong_batam_id')->references('id')->on('surat_jalan_tarik_kosong_batams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rit_batam_items', function (Blueprint $table) {
            $table->dropForeign('fk_sj_bongkaran_batam_id');
            $table->dropForeign('fk_sj_tarik_kosong_batam_id');
            $table->dropColumn(['surat_jalan_bongkaran_batam_id', 'surat_jalan_tarik_kosong_batam_id']);
            $table->unsignedBigInteger('surat_jalan_batam_id')->nullable(false)->change();
        });
    }
};
