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
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran_uang_rit ENUM(
            'belum_dibayar', 
            'proses_pranota', 
            'sudah_masuk_pranota',
            'pranota_submitted', 
            'pranota_approved', 
            'dibayar'
        ) DEFAULT 'belum_dibayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran_uang_rit ENUM(
            'belum_dibayar', 
            'proses_pranota', 
            'pranota_submitted', 
            'pranota_approved', 
            'dibayar'
        ) DEFAULT 'belum_dibayar'");
    }
};
