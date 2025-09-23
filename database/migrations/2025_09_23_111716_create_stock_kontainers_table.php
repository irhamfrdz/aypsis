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
        Schema::create('stock_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer')->unique();
            $table->string('ukuran')->nullable(); // 20ft, 40ft, dll
            $table->string('tipe_kontainer')->nullable(); // Dry, Reefer, dll
            $table->string('status')->default('available'); // available, rented, maintenance, damaged
            $table->string('lokasi')->nullable(); // Lokasi penyimpanan
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('kondisi')->default('baik'); // baik, rusak_ringan, rusak_berat
            $table->decimal('harga_sewa_per_hari', 15, 2)->nullable();
            $table->decimal('harga_sewa_per_bulan', 15, 2)->nullable();
            $table->string('pemilik')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->year('tahun_pembuatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_kontainers');
    }
};
