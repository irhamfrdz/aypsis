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
        Schema::create('invoice_aktivitas_lain_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_aktivitas_lain_id')->comment('FK ke invoice_aktivitas_lain');
            $table->unsignedBigInteger('pembayaran_invoice_aktivitas_lain_id')->comment('FK ke pembayaran_invoice_aktivitas_lain');
            $table->decimal('jumlah_dibayar', 15, 2)->default(0)->comment('Jumlah yang dibayarkan untuk invoice ini');
            $table->timestamps();

            // Foreign keys
            $table->foreign('invoice_aktivitas_lain_id', 'fk_invoice_pembayaran')
                  ->references('id')
                  ->on('invoice_aktivitas_lain')
                  ->onDelete('cascade');
            
            $table->foreign('pembayaran_invoice_aktivitas_lain_id', 'fk_pembayaran_invoice')
                  ->references('id')
                  ->on('pembayaran_invoice_aktivitas_lain')
                  ->onDelete('cascade');

            // Unique constraint to prevent duplicate payment for same invoice in same pembayaran
            $table->unique(['invoice_aktivitas_lain_id', 'pembayaran_invoice_aktivitas_lain_id'], 'unique_invoice_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_aktivitas_lain_pembayaran');
    }
};
