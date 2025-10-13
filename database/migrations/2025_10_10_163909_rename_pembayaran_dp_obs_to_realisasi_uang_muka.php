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
        // Rename tabel pembayaran_dp_obs menjadi realisasi_uang_muka
        Schema::rename('pembayaran_dp_obs', 'realisasi_uang_muka');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: rename kembali ke nama lama
        Schema::rename('realisasi_uang_muka', 'pembayaran_dp_obs');
    }
};
