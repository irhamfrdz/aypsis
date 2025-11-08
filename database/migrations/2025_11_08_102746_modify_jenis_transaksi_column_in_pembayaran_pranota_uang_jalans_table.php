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
        Schema::table('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            // Ubah kolom jenis_transaksi dari ENUM ke VARCHAR untuk mendukung nilai 'Kredit' dan 'Debit'
            $table->string('jenis_transaksi', 50)->default('transfer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            // Kembalikan ke ENUM semula jika perlu rollback
            $table->enum('jenis_transaksi', ['cash', 'transfer', 'check', 'giro'])->default('cash')->change();
        });
    }
};
