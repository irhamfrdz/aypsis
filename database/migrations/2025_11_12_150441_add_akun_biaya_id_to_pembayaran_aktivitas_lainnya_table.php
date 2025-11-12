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
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_biaya_id')->nullable()->after('nomor_accurate');
            $table->foreign('akun_biaya_id')->references('id')->on('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->dropForeign(['akun_biaya_id']);
            $table->dropColumn('akun_biaya_id');
        });
    }
};
