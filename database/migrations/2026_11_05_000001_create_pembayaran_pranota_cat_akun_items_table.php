<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_pranota_cat_akun_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_cat_id')
                ->constrained('pembayaran_pranota_cat', 'id', 'fk_ppcai_payment')->cascadeOnDelete();
            $table->foreignId('akun_coa_id')->constrained('akun_coa', 'id', 'fk_ppcai_coa')->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->string('memo', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_cat_akun_items');
    }
};
