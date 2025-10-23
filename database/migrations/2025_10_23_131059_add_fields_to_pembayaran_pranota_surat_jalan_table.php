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
            // Add fields to match pranota supir payment form
            $table->integer('nomor_cetakan')->nullable()->after('nomor_pembayaran');
            $table->string('bank')->nullable()->after('tanggal_pembayaran');
            $table->enum('jenis_transaksi', ['Debit', 'Kredit'])->nullable()->after('bank');
            $table->decimal('total_pembayaran', 15, 2)->nullable()->after('jumlah_pembayaran');
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0)->after('total_pembayaran');
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2)->nullable()->after('total_tagihan_penyesuaian');
            $table->text('alasan_penyesuaian')->nullable()->after('total_tagihan_setelah_penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_surat_jalan', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_cetakan',
                'bank',
                'jenis_transaksi',
                'total_pembayaran',
                'total_tagihan_penyesuaian',
                'total_tagihan_setelah_penyesuaian',
                'alasan_penyesuaian'
            ]);
        });
    }
};
