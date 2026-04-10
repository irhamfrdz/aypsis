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
        Schema::create('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_cetakan')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank')->nullable();
            $table->enum('jenis_transaksi', ['cash', 'transfer', 'check', 'giro'])->default('cash');
            $table->decimal('total_pembayaran', 15, 2);
            $table->text('keterangan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status_pembayaran', 'tanggal_pembayaran'], 'idx_status_tanggal');
            $table->index(['jenis_transaksi'], 'idx_jenis_transaksi');
            $table->index(['created_at'], 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_biaya_kapals');
    }
};
