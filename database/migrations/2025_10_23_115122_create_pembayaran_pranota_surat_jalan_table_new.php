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
        Schema::create('pembayaran_pranota_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_surat_jalan_id')->constrained('pranota_surat_jalans')->onDelete('cascade');
            $table->string('nomor_pembayaran')->unique();
            $table->datetime('tanggal_pembayaran');
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'giro']);
            $table->string('nomor_referensi')->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2);
            $table->text('keterangan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'paid', 'partial', 'cancelled'])->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes dengan nama yang lebih pendek
            $table->index(['pranota_surat_jalan_id', 'status_pembayaran'], 'ppsj_pranota_status_idx');
            $table->index('tanggal_pembayaran', 'ppsj_tanggal_idx');
            $table->index('nomor_pembayaran', 'ppsj_nomor_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_surat_jalan');
    }
};
