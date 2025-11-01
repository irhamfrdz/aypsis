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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->enum('status_pembayaran_uang_rit', [
                'belum_dibayar', 
                'proses_pranota', 
                'pranota_submitted', 
                'pranota_approved', 
                'dibayar'
            ])->default('belum_dibayar')->after('status');
            
            $table->index('status_pembayaran_uang_rit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropIndex(['status_pembayaran_uang_rit']);
            $table->dropColumn('status_pembayaran_uang_rit');
        });
    }
};
