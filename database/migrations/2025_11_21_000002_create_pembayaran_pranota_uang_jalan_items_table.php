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
        Schema::create('pembayaran_pranota_uang_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_uang_jalan_id');
            $table->unsignedBigInteger('pranota_uang_jalan_id');
            $table->decimal('subtotal', 15, 2)->comment('Subtotal untuk pranota ini');
            $table->timestamps();
            
            // Foreign keys with custom short names
            $table->foreign('pembayaran_pranota_uang_jalan_id', 'fk_ppuj_items_pembayaran')
                  ->references('id')
                  ->on('pembayaran_pranota_uang_jalans')
                  ->onDelete('cascade');
                  
            $table->foreign('pranota_uang_jalan_id', 'fk_ppuj_items_pranota')
                  ->references('id')
                  ->on('pranota_uang_jalans')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['pembayaran_pranota_uang_jalan_id'], 'idx_pembayaran_id');
            $table->index(['pranota_uang_jalan_id'], 'idx_pranota_id');
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['pembayaran_pranota_uang_jalan_id', 'pranota_uang_jalan_id'], 'unique_pembayaran_pranota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_uang_jalan_items');
    }
};
