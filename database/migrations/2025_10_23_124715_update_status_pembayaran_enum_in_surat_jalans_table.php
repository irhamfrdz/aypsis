<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            // Update existing records to new enum values
            DB::statement("UPDATE surat_jalans SET status_pembayaran = 'belum_dibayar' WHERE status_pembayaran IN ('belum_bayar', 'sebagian')");
            DB::statement("UPDATE surat_jalans SET status_pembayaran = 'sudah_dibayar' WHERE status_pembayaran = 'lunas'");

            // Drop and recreate the enum column with new values
            DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran ENUM('belum_dibayar', 'sudah_dibayar') NOT NULL DEFAULT 'belum_dibayar'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            // Revert back to original enum values
            DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran ENUM('belum_bayar', 'sebagian', 'lunas') NOT NULL DEFAULT 'belum_bayar'");

            // Update records back to original values
            DB::statement("UPDATE surat_jalans SET status_pembayaran = 'belum_bayar' WHERE status_pembayaran = 'belum_dibayar'");
            DB::statement("UPDATE surat_jalans SET status_pembayaran = 'lunas' WHERE status_pembayaran = 'sudah_dibayar'");
        });
    }
};
