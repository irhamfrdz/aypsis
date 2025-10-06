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
        Schema::create('pembayaran_pranota_kontainer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_kontainer_id');
            $table->unsignedBigInteger('pranota_id');
            $table->decimal('amount', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pembayaran_pranota_kontainer_id', 'fk_pembayaran_pranota_kontainer')
                  ->references('id')->on('pembayaran_pranota_kontainer')
                  ->onDelete('cascade');
            $table->foreign('pranota_id')->references('id')->on('pranotalist')->onDelete('cascade');

            // Ensure each pranota can only be paid once
            $table->unique('pranota_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_kontainer_items');
    }
};
