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
            $table->unsignedBigInteger('surat_jalan_id');
            $table->decimal('jumlah_uang_supir', 12, 2)->default(0);
            $table->decimal('jumlah_uang_kenek', 12, 2)->default(0);
            $table->decimal('total_uang_jalan', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_pemberian');
            $table->enum('status', ['belum_dibayar', 'sudah_dibayar', 'ditolak'])->default('belum_dibayar');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('surat_jalan_id');
            $table->index('status');
            $table->index('tanggal_pemberian');
            $table->index('created_by');
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
