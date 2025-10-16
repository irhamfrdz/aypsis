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
        Schema::create('pranota_surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->string('periode_tagihan', 20);
            $table->integer('jumlah_surat_jalan')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['status', 'tanggal_pranota']);
            $table->index('periode_tagihan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_surat_jalans');
    }
};