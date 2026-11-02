<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_pranota_ob_akun_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_ob_id')->constrained('pembayaran_pranota_obs')->cascadeOnDelete();
            $table->foreignId('akun_coa_id')->constrained('akun_coa')->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
        });

        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->foreignId('akun_dp_id')->nullable()->constrained('akun_coa')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('akun_dp_id');
        });
        Schema::dropIfExists('pembayaran_pranota_ob_akun_items');
    }
};
