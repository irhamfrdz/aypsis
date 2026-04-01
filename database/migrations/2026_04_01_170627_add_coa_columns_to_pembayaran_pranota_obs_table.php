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
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_coa_id')->nullable()->after('jenis_transaksi');
            $table->unsignedBigInteger('akun_bank_id')->nullable()->after('akun_coa_id');
            
            $table->foreign('akun_coa_id')->references('id')->on('akun_coa')->onDelete('set null');
            $table->foreign('akun_bank_id')->references('id')->on('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->dropForeign(['akun_coa_id']);
            $table->dropForeign(['akun_bank_id']);
            $table->dropColumn(['akun_coa_id', 'akun_bank_id']);
        });
    }
};
