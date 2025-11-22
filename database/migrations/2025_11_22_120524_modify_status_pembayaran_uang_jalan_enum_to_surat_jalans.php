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
        // Modify ENUM to add 'dibayar' value
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran_uang_jalan ENUM('belum_ada', 'sudah_masuk_uang_jalan', 'dibayar') DEFAULT 'belum_ada' COMMENT 'Status pembayaran uang jalan: belum_ada = belum dibuat uang jalan, sudah_masuk_uang_jalan = sudah ada record uang jalan, dibayar = sudah dibayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status_pembayaran_uang_jalan ENUM('belum_ada', 'sudah_masuk_uang_jalan') DEFAULT 'belum_ada' COMMENT 'Status pembayaran uang jalan: belum_ada = belum dibuat uang jalan, sudah_masuk_uang_jalan = sudah ada record uang jalan'");
    }
};
