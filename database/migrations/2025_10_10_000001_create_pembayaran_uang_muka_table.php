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
        Schema::create('pembayaran_uang_muka', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->date('tanggal_pembayaran');
            $table->foreignId('kas_bank_id')->constrained('akun_coa')->onDelete('cascade');
            $table->enum('jenis_transaksi', ['debit', 'kredit']);
            $table->string('kegiatan'); // Field baru untuk kegiatan
            $table->json('supir_ids'); // Array supir yang dipilih
            $table->json('jumlah_per_supir'); // JSON object dengan supir_id => jumlah
            $table->decimal('total_pembayaran', 15, 2); // Total pembayaran uang muka
            $table->text('keterangan')->nullable();
            $table->enum('status', ['uang_muka_belum_terpakai', 'uang_muka_terpakai'])->default('uang_muka_belum_terpakai');
            $table->unsignedBigInteger('dibuat_oleh');
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('dibuat_oleh')->references('id')->on('users');
            $table->foreign('disetujui_oleh')->references('id')->on('users');

            // Indexes
            $table->index('nomor_pembayaran');
            $table->index('tanggal_pembayaran');
            $table->index('status');
            $table->index('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_uang_muka');
    }
};
