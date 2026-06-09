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
        Schema::table('pembayaran_pranota_ongkos_truks', function (Blueprint $table) {
            $table->string('jenis_transaksi', 50)->default('Kredit')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_ongkos_truks', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['cash', 'transfer', 'check', 'giro'])->default('cash')->change();
        });
    }
};
