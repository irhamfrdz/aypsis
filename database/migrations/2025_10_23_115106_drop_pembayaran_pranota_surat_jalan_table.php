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
        // Drop table pembayaran_pranota_surat_jalan jika ada
        Schema::dropIfExists('pembayaran_pranota_surat_jalan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu membuat ulang karena akan dibuat di migrasi berikutnya
        // Migrasi ini hanya untuk cleanup
    }
};
