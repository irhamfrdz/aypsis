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
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->unsignedBigInteger('bl_id')->nullable()->after('nomor_voyage');
            $table->unsignedBigInteger('klasifikasi_biaya_id')->nullable()->after('bl_id');
            $table->json('barang_detail')->nullable()->after('klasifikasi_biaya_id');
            $table->text('deskripsi')->nullable()->after('total');
            $table->text('catatan')->nullable()->after('deskripsi');
            
            // Add foreign key constraints if needed
            // $table->foreign('bl_id')->references('id')->on('bls')->onDelete('set null');
            // $table->foreign('klasifikasi_biaya_id')->references('id')->on('klasifikasi_biayas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->dropColumn(['bl_id', 'klasifikasi_biaya_id', 'barang_detail', 'deskripsi', 'catatan']);
        });
    }
};
