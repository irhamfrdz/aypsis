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
        Schema::create('pembayaran_aktivitas_lainnya_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran_aktivitas_lainnya')->onDelete('cascade');
            $table->foreignId('aktivitas_id')->constrained('aktivitas_lainnya')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pembayaran_id', 'aktivitas_id'], 'pay_akt_lain_unique');
            $table->index('pembayaran_id');
            $table->index('aktivitas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_items');
    }
};
