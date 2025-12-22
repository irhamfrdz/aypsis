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
        Schema::create('pembayaran_invoice_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('jumlah_dibayar', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('pembayaran_id')
                ->references('id')
                ->on('pembayaran_aktivitas_lains')
                ->onDelete('cascade');

            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoice_aktivitas_lain')
                ->onDelete('cascade');

            $table->unique(['pembayaran_id', 'invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_invoice_pivot');
    }
};
