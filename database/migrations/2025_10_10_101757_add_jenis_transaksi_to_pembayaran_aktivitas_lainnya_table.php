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
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'jenis_transaksi')) {
                $table->enum('jenis_transaksi', ['debit', 'kredit'])->default('kredit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'jenis_transaksi')) {
                $table->dropColumn('jenis_transaksi');
            }
        });
    }
};
