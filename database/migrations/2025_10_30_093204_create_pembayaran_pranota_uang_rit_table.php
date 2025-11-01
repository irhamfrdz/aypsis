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
        Schema::create('pembayaran_pranota_uang_rit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_uang_rit_id');
            $table->unsignedBigInteger('pranota_uang_rit_id');
            $table->integer('uang_jalan_dibayar'); // Amount of road money being paid
            $table->integer('uang_rit_dibayar'); // Amount of trip money being paid
            $table->timestamps();

            // Foreign keys
            $table->foreign('pembayaran_uang_rit_id')->references('id')->on('pembayaran_uang_rits')->onDelete('cascade');
            $table->foreign('pranota_uang_rit_id')->references('id')->on('pranota_uang_rits')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['pembayaran_uang_rit_id', 'pranota_uang_rit_id'], 'pembayaran_pranota_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_uang_rit');
    }
};
