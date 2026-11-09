<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_pranota_vendor_supir_coa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran_pranota_vendor_supirs')->cascadeOnDelete();
            $table->foreignId('akun_coa_id')->constrained('akun_coa')->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->string('memo', 500)->nullable();
            $table->timestamps();
            $table->index(['pembayaran_id', 'akun_coa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_vendor_supir_coa_items');
    }
};
