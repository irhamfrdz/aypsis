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
        Schema::create('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->integer('nomor_cetakan')->default(1);
            $table->string('bank');
            $table->enum('jenis_transaksi', ['debit', 'credit', 'transfer'])->default('debit');
            $table->date('tanggal_kas');
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            $table->decimal('penyesuaian', 15, 2)->default(0);
            $table->decimal('total_setelah_penyesuaian', 15, 2)->default(0);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->json('pranota_ob_ids')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('nomor_pembayaran');
            $table->index('tanggal_kas');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_obs');
    }
};
