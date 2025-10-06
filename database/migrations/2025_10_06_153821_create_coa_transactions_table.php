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
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('akun_coa')->onDelete('cascade');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi')->nullable(); // Nomor pembayaran, invoice, dll
            $table->string('jenis_transaksi'); // 'pembayaran', 'penerimaan', 'adjustment', dll
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0); // Running balance setelah transaksi
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index('coa_id');
            $table->index('tanggal_transaksi');
            $table->index('nomor_referensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_transactions');
    }
};
