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
        Schema::create('stock_ban_dalams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nama_stock_ban_id');
            $table->string('nomor_bukti')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('type')->default('pcs'); // kondisi became type
            $table->integer('qty')->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->date('tanggal_masuk')->nullable();
            $table->string('lokasi')->default('Gudang Utama');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ban_dalams');
    }
};
