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
        Schema::create('pembayaran_dp_obs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->date('tanggal_pembayaran');
            $table->foreignId('kas_bank_id')->constrained('akun_coa')->onDelete('cascade');
            $table->enum('jenis_transaksi', ['debit', 'kredit']);
            $table->json('supir_ids'); // Array supir yang dipilih
            $table->decimal('jumlah_per_supir', 15, 2);
            $table->decimal('total_pembayaran', 15, 2); // jumlah_per_supir * count(supir_ids)
            $table->text('keterangan')->nullable();
            $table->enum('status', ['dp_belum_terpakai', 'dp_terpakai'])->default('dp_belum_terpakai');
            $table->unsignedBigInteger('dibuat_oleh');
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('dibuat_oleh')->references('id')->on('users');
            $table->foreign('disetujui_oleh')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_dp_obs');
    }
};
