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
        Schema::create('stock_bans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_seri')->unique();
            $table->string('merk');
            $table->string('ukuran');
            $table->enum('kondisi', ['Baru', 'Vulkanisir', 'Bekas', 'Afkir'])->default('Baru');
            $table->enum('status', ['Stok', 'Terpakai', 'Rusak', 'Hilang'])->default('Stok');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->date('tanggal_masuk')->nullable();
            $table->string('lokasi')->default('Gudang Utama');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_bans');
    }
};
