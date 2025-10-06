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
        Schema::create('pembayaran_pranota_cat_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_cat_id')->constrained('pembayaran_pranota_cat')->onDelete('cascade');
            $table->foreignId('pranota_tagihan_cat_id')->constrained('pranota_tagihan_cat')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            // Ensure unique combination with shorter name
            $table->unique(['pembayaran_pranota_cat_id', 'pranota_tagihan_cat_id'], 'pp_cat_items_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_cat_items');
    }
};
