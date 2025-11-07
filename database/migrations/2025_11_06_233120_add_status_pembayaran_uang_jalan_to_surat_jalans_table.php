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
            // Add status_pembayaran_uang_jalan column after status_pembayaran column
            $table->enum('status_pembayaran_uang_jalan', ['belum_ada', 'sudah_masuk_uang_jalan'])
                  ->default('belum_ada')
                  ->after('status_pembayaran')
                  ->comment('Status pembayaran uang jalan: belum_ada = belum dibuat uang jalan, sudah_masuk_uang_jalan = sudah ada record uang jalan');
            
            // Add index for better performance
            $table->index('status_pembayaran_uang_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropIndex(['status_pembayaran_uang_jalan']);
            $table->dropColumn('status_pembayaran_uang_jalan');
        });
    }
};
