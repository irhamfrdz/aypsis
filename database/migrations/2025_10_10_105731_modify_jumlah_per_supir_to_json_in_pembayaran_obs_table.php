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
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Ubah kolom jumlah_per_supir dari decimal ke JSON
            $table->json('jumlah_per_supir')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Kembalikan ke decimal
            $table->decimal('jumlah_per_supir', 15, 2)->change();
        });
    }
};
