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
            // Rename existing columns to match form
            $table->renameColumn('penyesuaian', 'total_tagihan_penyesuaian');
            $table->renameColumn('total_setelah_penyesuaian', 'total_tagihan_setelah_penyesuaian');

            // Update jenis_transaksi enum to match form options
            $table->enum('jenis_transaksi', ['transfer', 'tunai', 'cek', 'giro'])->change();

            // Remove nomor_cetakan since it's not used in form
            $table->dropColumn('nomor_cetakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            // Revert column renames
            $table->renameColumn('total_tagihan_penyesuaian', 'penyesuaian');
            $table->renameColumn('total_tagihan_setelah_penyesuaian', 'total_setelah_penyesuaian');

            // Revert jenis_transaksi enum
            $table->enum('jenis_transaksi', ['Debit', 'Kredit'])->change();

            // Add back nomor_cetakan
            $table->string('nomor_cetakan')->nullable();
        });
    }
};
