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
        Schema::table('pembayaran_biaya_kapals', function (Blueprint $table) {
            // Ubah kolom jenis_transaksi dari ENUM lama ke ENUM baru yang mendukung debit/kredit
            // Atau lebih baik menggunakan string agar lebih fleksibel seperti di tabel lain
            $table->string('jenis_transaksi', 50)->default('kredit')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['cash', 'transfer', 'check', 'giro'])->default('cash')->change();
        });
    }
};
