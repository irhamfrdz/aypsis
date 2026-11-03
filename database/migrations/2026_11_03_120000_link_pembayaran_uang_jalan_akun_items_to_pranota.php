<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) {
            $table->foreignId('pranota_uang_jalan_id')->nullable()->after('akun_coa_id')
                ->constrained('pranota_uang_jalans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pranota_uang_jalan_id');
        });
    }
};
