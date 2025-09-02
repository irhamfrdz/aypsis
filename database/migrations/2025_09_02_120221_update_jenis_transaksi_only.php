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
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            // Update jenis_transaksi enum to Debit/Kredit only
            $table->enum('jenis_transaksi', ['Debit', 'Kredit'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            // Revert to original values
            $table->enum('jenis_transaksi', ['transfer', 'tunai', 'cek', 'giro'])->change();
        });
    }
};
