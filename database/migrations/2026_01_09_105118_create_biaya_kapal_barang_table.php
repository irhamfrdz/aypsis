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
        Schema::create('biaya_kapal_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->unsignedBigInteger('pricelist_buruh_id');
            $table->integer('jumlah');
            $table->decimal('tarif', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->foreign('biaya_kapal_id')->references('id')->on('biaya_kapals')->onDelete('cascade');
            $table->foreign('pricelist_buruh_id')->references('id')->on('pricelist_buruh')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_barang');
    }
};
