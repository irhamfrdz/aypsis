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
        Schema::table('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            // Add jenis_transaksi column if it doesn't exist
            if (!Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'jenis_transaksi')) {
                $table->enum('jenis_transaksi', ['Debit', 'Kredit'])->default('Debit')->after('nominal_pembayaran');
            } else {
                // If column exists, just change the enum values
                $table->enum('jenis_transaksi', ['Debit', 'Kredit'])->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['debit', 'credit'])->change();
        });
    }
};
