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
        Schema::create('pembayaran_pranota_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_id')->constrained('pembayaran_pranota')->onDelete('cascade');
            $table->foreignId('pranota_id')->constrained('pranotalist')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            // Ensure unique combination
            $table->unique(['pembayaran_pranota_id', 'pranota_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_items');
    }
};
