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
            $table->enum('status_pembayaran_uang_rit_kenek', [
                'belum_dibayar', 
                'proses_pranota', 
                'sudah_masuk_pranota',
                'pranota_submitted', 
                'pranota_approved', 
                'dibayar'
            ])->default('belum_dibayar')->after('status_pembayaran_uang_rit');
            
            // Add index for better query performance
            $table->index('status_pembayaran_uang_rit_kenek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropIndex(['status_pembayaran_uang_rit_kenek']);
            $table->dropColumn('status_pembayaran_uang_rit_kenek');
        });
    }
};
