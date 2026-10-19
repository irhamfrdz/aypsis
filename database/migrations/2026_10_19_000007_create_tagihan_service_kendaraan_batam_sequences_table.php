<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_service_kendaraan_batam_sequences', function (Blueprint $table) {
            $table->string('kode_tanggal_bulan', 7)->primary();
            $table->unsignedInteger('nomor_terakhir')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_service_kendaraan_batam_sequences');
    }
};
