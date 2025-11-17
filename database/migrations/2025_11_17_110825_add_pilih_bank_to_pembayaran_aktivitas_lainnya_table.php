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
            $table->unsignedBigInteger('pilih_bank')->nullable()->after('nomor_accurate');
            $table->foreign('pilih_bank')->references('id')->on('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->dropForeign(['pilih_bank']);
            $table->dropColumn('pilih_bank');
        });
    }
};
