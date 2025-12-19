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
        Schema::create('pembayaran_invoice_aktivitas_lain', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique()->comment('Nomor pembayaran');
            $table->string('nomor_accurate')->nullable()->comment('Nomor dari sistem Accurate');
            $table->date('tanggal')->comment('Tanggal pembayaran');
            $table->string('jenis_aktivitas')->nullable()->comment('Jenis aktivitas (bisa multiple)');
            $table->string('penerima')->nullable()->comment('Penerima (bisa multiple)');
            $table->decimal('total_invoice', 15, 2)->default(0)->comment('Total dari semua invoice yang dipilih');
            $table->decimal('jumlah_dibayar', 15, 2)->default(0)->comment('Jumlah yang dibayarkan');
            $table->enum('debit_kredit', ['debit', 'kredit'])->comment('Jenis transaksi');
            $table->unsignedBigInteger('akun_coa_id')->comment('Akun COA untuk biaya');
            $table->unsignedBigInteger('akun_bank_id')->comment('Akun bank untuk pembayaran');
            $table->text('keterangan')->nullable()->comment('Keterangan pembayaran');
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->unsignedBigInteger('created_by')->comment('User yang membuat');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('User yang approve');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('akun_coa_id')->references('id')->on('akun_coa')->onDelete('restrict');
            $table->foreign('akun_bank_id')->references('id')->on('akun_coa')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_invoice_aktivitas_lain');
    }
};
