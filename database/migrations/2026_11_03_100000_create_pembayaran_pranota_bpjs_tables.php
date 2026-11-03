<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_pranota_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran', 100)->unique();
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->unsignedBigInteger('bank_coa_id');
            $table->unsignedBigInteger('akun_coa_id');
            $table->decimal('total_pembayaran', 15, 2);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_bpjs_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_bpjs_id')->constrained('pembayaran_pranota_bpjs');
            $table->foreignId('pranota_bpjs_header_id')->unique()->constrained('pranota_bpjs_headers');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_bpjs_items');
        Schema::dropIfExists('pembayaran_pranota_bpjs');
    }
};
