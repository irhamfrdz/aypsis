<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pembayaran_ob_akun_items')) {
            Schema::create('pembayaran_ob_akun_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembayaran_ob_id')->constrained('pembayaran_obs')->cascadeOnDelete();
                $table->foreignId('akun_coa_id')->constrained('akun_coa')->restrictOnDelete();
                $table->decimal('nominal', 15, 2);
                $table->text('memo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_ob_akun_items');
    }
};
