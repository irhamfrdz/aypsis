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
        Schema::create('uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->onDelete('cascade');
            $table->string('nomor_uang_jalan')->unique();
            $table->decimal('jumlah_uang_supir', 12, 2)->default(0);
            $table->decimal('jumlah_uang_kenek', 12, 2)->default(0);
            $table->decimal('total_uang_jalan', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_pemberian');
            $table->date('tanggal_uang_jalan')->nullable();
            $table->enum('status', ['belum_dibayar', 'belum_masuk_pranota', 'sudah_masuk_pranota', 'lunas', 'dibatalkan'])->default('belum_dibayar');
            $table->enum('bank_kas', ['bank', 'kas'])->default('kas');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'tanggal_pemberian']);
            $table->index('nomor_uang_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_jalans');
    }
};
