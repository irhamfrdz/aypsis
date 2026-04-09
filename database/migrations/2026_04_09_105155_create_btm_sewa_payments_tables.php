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
        Schema::create('btm_sewa_payments', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->enum('jenis_transaksi', ['Debit', 'Kredit']);
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_penyesuaian', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status')->default('PAID');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('btm_sewa_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('btm_sewa_payment_id')->constrained('btm_sewa_payments')->onDelete('cascade');
            $table->foreignId('btm_sewa_pranota_id')->constrained('btm_sewa_pranotas')->onDelete('cascade');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btm_sewa_payment_details');
        Schema::dropIfExists('btm_sewa_payments');
    }
};
