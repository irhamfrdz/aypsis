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
        Schema::table('pembayaran_pranota_surat_jalan', function (Blueprint $table) {
            // Drop unused fields
            $table->dropColumn(['jumlah_pembayaran', 'metode_pembayaran', 'nomor_referensi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_surat_jalan', function (Blueprint $table) {
            // Re-add the dropped fields if migration is rolled back
            $table->decimal('jumlah_pembayaran', 15, 2)->nullable()->after('jenis_transaksi');
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'giro'])->nullable()->after('jenis_transaksi');
            $table->string('nomor_referensi')->nullable()->after('metode_pembayaran');
        });
    }
};
