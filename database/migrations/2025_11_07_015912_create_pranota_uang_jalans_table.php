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
        Schema::create('pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota', 50)->unique();
            $table->date('tanggal_pranota');
            $table->string('periode_tagihan', 20);
            $table->integer('jumlah_uang_jalan')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status_pembayaran', ['unpaid', 'paid', 'partial', 'cancelled'])->default('unpaid');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nomor_pranota');
            $table->index('status_pembayaran');
            $table->index('periode_tagihan');
            $table->index('created_by');

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_jalans');
    }
};
