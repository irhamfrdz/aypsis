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
            // Drop existing column
            $table->dropColumn('status_pembayaran');
        });

        Schema::table('surat_jalans', function (Blueprint $table) {
            // Add new column with correct enum values
            $table->enum('status_pembayaran', ['belum_dibayar', 'sudah_dibayar'])
                  ->default('belum_dibayar')
                  ->after('status')
                  ->comment('Status pembayaran: belum_dibayar, sudah_dibayar');

            $table->index(['status_pembayaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropIndex(['status_pembayaran']);
            $table->dropColumn('status_pembayaran');
        });

        Schema::table('surat_jalans', function (Blueprint $table) {
            // Restore original column
            $table->enum('status_pembayaran', ['belum_bayar', 'sebagian', 'lunas'])
                  ->default('belum_bayar')
                  ->after('status');
        });
    }
};
