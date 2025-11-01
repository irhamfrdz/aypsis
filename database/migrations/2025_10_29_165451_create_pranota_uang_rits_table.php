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
        Schema::create('pranota_uang_rits', function (Blueprint $table) {
            $table->id();
            $table->string('no_pranota')->unique(); // Nomor pranota
            $table->date('tanggal'); // Tanggal pranota
            $table->unsignedBigInteger('surat_jalan_id')->nullable(); // Foreign key ke surat jalan
            $table->string('no_surat_jalan')->nullable(); // Nomor surat jalan (backup)
            $table->string('supir_nama'); // Nama supir
            $table->string('no_plat'); // Nomor polisi kendaraan
            $table->decimal('uang_jalan', 15, 2)->default(0); // Uang jalan
            $table->decimal('uang_rit', 15, 2)->default(0); // Uang rit
            $table->decimal('total_uang', 15, 2)->default(0); // Total uang jalan + uang rit
            $table->text('keterangan')->nullable(); // Keterangan tambahan
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->date('tanggal_bayar')->nullable(); // Tanggal pembayaran
            $table->unsignedBigInteger('created_by')->nullable(); // User yang membuat
            $table->unsignedBigInteger('updated_by')->nullable(); // User yang mengupdate
            $table->unsignedBigInteger('approved_by')->nullable(); // User yang approve
            $table->timestamp('approved_at')->nullable(); // Waktu approve
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['tanggal', 'status']);
            $table->index('no_surat_jalan');
            $table->index('supir_nama');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_rits');
    }
};
