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
        Schema::create('pembayaran_biaya_kapal_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_biaya_kapal_id');
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('pembayaran_biaya_kapal_id', 'fk_pbk_items_pembayaran')
                  ->references('id')
                  ->on('pembayaran_biaya_kapals')
                  ->onDelete('cascade');
                  
            $table->foreign('biaya_kapal_id', 'fk_pbk_items_biaya_kapal')
                  ->references('id')
                  ->on('biaya_kapals')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['pembayaran_biaya_kapal_id'], 'idx_pbk_id');
            $table->index(['biaya_kapal_id'], 'idx_bk_id');
            
            // Unique constraint
            $table->unique(['pembayaran_biaya_kapal_id', 'biaya_kapal_id'], 'unique_pbk_bk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_biaya_kapal_items');
    }
};
