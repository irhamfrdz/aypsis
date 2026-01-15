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
        Schema::table('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_coa_id')->nullable()->after('tanggal');
            $table->enum('tipe_transaksi', ['debit', 'kredit'])->nullable()->after('akun_coa_id');
            $table->decimal('nominal_debit', 15, 2)->nullable()->after('tipe_transaksi');
            $table->decimal('nominal_kredit', 15, 2)->nullable()->after('nominal_debit');
            
            $table->foreign('akun_coa_id')->references('id')->on('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->dropForeign(['akun_coa_id']);
            $table->dropColumn(['akun_coa_id', 'tipe_transaksi', 'nominal_debit', 'nominal_kredit']);
        });
    }
};
