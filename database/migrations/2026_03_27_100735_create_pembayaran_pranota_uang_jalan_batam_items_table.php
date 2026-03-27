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
        Schema::create('pembayaran_pranota_uang_jalan_batam_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_uang_jalan_btm_id')
                  ->constrained('pembayaran_pranota_uang_jalan_batams', 'id')
                  ->onDelete('cascade')
                  ->name('puj_btm_items_puj_btm_id_foreign');
            $table->foreignId('pranota_uang_jalan_batam_id')
                  ->constrained('pranota_uang_jalan_batams')
                  ->onDelete('cascade')
                  ->name('puj_btm_items_puj_btm_pranota_id_foreign');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_uang_jalan_batam_items');
    }
};
