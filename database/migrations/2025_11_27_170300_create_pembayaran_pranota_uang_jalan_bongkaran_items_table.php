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
        Schema::create('pembayaran_pranota_uang_jalan_bongkaran_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_uang_jalan_bongkaran_id');
            $table->unsignedBigInteger('pranota_uang_jalan_bongkaran_id');
            $table->decimal('subtotal', 15, 2)->comment('Subtotal untuk pranota ini');
            $table->timestamps();

            $table->foreign('pembayaran_pranota_uang_jalan_bongkaran_id', 'fk_ppujb_items_pembayaran')
                  ->references('id')
                  ->on('pembayaran_pranota_uang_jalan_bongkarans')
                  ->onDelete('cascade');

            $table->foreign('pranota_uang_jalan_bongkaran_id', 'fk_ppujb_items_pranota')
                  ->references('id')
                  ->on('pranota_uang_jalan_bongkarans')
                  ->onDelete('cascade');

            // Indexes
            $table->index(['pembayaran_pranota_uang_jalan_bongkaran_id'], 'idx_pembayaran_bongkaran_id');
            $table->index(['pranota_uang_jalan_bongkaran_id'], 'idx_pranota_bongkaran_id');

            // Unique constraint
            $table->unique(['pembayaran_pranota_uang_jalan_bongkaran_id', 'pranota_uang_jalan_bongkaran_id'], 'unique_pembayaran_pranota_bongkaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_uang_jalan_bongkaran_items');
    }
};
