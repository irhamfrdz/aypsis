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
        Schema::create('pembayaran_pranota_lembur_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_lembur_id')
                  ->constrained('pembayaran_pranota_lemburs')
                  ->onDelete('cascade')
                  ->name('fk_pemb_lembur_id');
            $table->foreignId('pranota_lembur_id')
                  ->constrained('pranota_lemburs')
                  ->onDelete('restrict')
                  ->name('fk_pemb_pranota_lembur_id');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
            
            // Indexes
            $table->index(['pembayaran_pranota_lembur_id'], 'idx_pemb_lembur_item_pay_id');
            $table->index(['pranota_lembur_id'], 'idx_pemb_lembur_item_pran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_lembur_items');
    }
};
