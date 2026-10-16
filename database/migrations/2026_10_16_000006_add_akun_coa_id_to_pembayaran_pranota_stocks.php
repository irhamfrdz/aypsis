<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_stocks', function (Blueprint $table) {
            $table->foreignId('akun_coa_id')->nullable()->constrained('akun_coa')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_stocks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('akun_coa_id');
        });
    }
};
