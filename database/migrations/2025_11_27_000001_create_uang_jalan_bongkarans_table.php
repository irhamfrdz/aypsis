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
        Schema::create('uang_jalan_bongkarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_uang_jalan')->nullable();
            $table->date('tanggal_uang_jalan');
            $table->foreignId('surat_jalan_bongkaran_id')->constrained('surat_jalan_bongkarans')->onDelete('cascade');
            $table->string('kegiatan_bongkar_muat')->nullable();
            $table->decimal('jumlah_uang_jalan', 15, 2)->default(0);
            $table->decimal('jumlah_mel', 15, 2)->default(0);
            $table->decimal('jumlah_pelancar', 15, 2)->default(0);
            $table->decimal('jumlah_kawalan', 15, 2)->default(0);
            $table->decimal('jumlah_parkir', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('alasan_penyesuaian')->nullable();
            $table->decimal('jumlah_penyesuaian', 15, 2)->default(0);
            $table->decimal('jumlah_total', 15, 2)->default(0);
            $table->text('memo')->nullable();
            $table->enum('status', ['belum_dibayar', 'belum_masuk_pranota', 'sudah_masuk_pranota', 'lunas', 'dibatalkan'])->default('belum_dibayar');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal_uang_jalan', 'status']);
            $table->index('surat_jalan_bongkaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_jalan_bongkarans');
    }
};