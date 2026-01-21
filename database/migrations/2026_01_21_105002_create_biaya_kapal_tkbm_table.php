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
        Schema::create('biaya_kapal_tkbm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->unsignedBigInteger('pricelist_tkbm_id');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_nominal', 15, 2)->nullable();
            $table->decimal('dp', 15, 2)->nullable();
            $table->decimal('sisa_pembayaran', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('biaya_kapal_id')->references('id')->on('biaya_kapals')->onDelete('cascade');
            $table->foreign('pricelist_tkbm_id')->references('id')->on('pricelist_tkbms')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_tkbm');
    }
};
