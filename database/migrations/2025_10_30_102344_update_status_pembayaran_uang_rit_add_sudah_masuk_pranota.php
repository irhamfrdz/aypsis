<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran_uang_rit ENUM(
            'belum_dibayar', 
            'proses_pranota', 
            'sudah_masuk_pranota',
            'pranota_submitted', 
            'pranota_approved', 
            'dibayar'
        ) DEFAULT 'belum_dibayar'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran_uang_rit ENUM(
            'belum_dibayar', 
            'proses_pranota', 
            'pranota_submitted', 
            'pranota_approved', 
            'dibayar'
        ) DEFAULT 'belum_dibayar'");
        }
    }
};
