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
        Schema::table('kontainer_perjalanans', function (Blueprint $table) {
            // Tambahkan kolom gudang_tujuan_id setelah kolom tujuan_pengiriman
            $table->unsignedBigInteger('gudang_tujuan_id')->nullable()->after('tujuan_pengiriman');
            
            // Tambahkan foreign key ke tabel gudangs
            $table->foreign('gudang_tujuan_id')
                  ->references('id')
                  ->on('gudangs')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainer_perjalanans', function (Blueprint $table) {
            // Drop foreign key terlebih dahulu
            $table->dropForeign(['gudang_tujuan_id']);
            // Drop kolom
            $table->dropColumn('gudang_tujuan_id');
        });
    }
};
