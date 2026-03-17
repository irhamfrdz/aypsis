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
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->string('nomor_pembayaran')->nullable()->after('no_surat_jalan');
            $table->string('nomor_accurate')->nullable()->after('nomor_pembayaran');
            $table->date('tanggal_kas')->nullable()->after('nomor_accurate');
            $table->date('tanggal_pembayaran')->nullable()->after('tanggal_kas');
            $table->string('bank')->nullable()->after('tanggal_pembayaran');
            $table->string('jenis_transaksi', 20)->nullable()->after('bank');
            $table->decimal('total_pembayaran', 18, 2)->nullable()->after('jenis_transaksi');
            $table->decimal('total_tagihan_penyesuaian', 18, 2)->nullable()->after('total_pembayaran');
            $table->decimal('total_tagihan_setelah_penyesuaian', 18, 2)->nullable()->after('total_tagihan_penyesuaian');
            $table->text('alasan_penyesuaian')->nullable()->after('total_tagihan_setelah_penyesuaian');
            $table->text('keterangan')->nullable()->after('alasan_penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_pembayaran',
                'nomor_accurate',
                'tanggal_kas',
                'tanggal_pembayaran',
                'bank',
                'jenis_transaksi',
                'total_pembayaran',
                'total_tagihan_penyesuaian',
                'total_tagihan_setelah_penyesuaian',
                'alasan_penyesuaian',
                'keterangan',
            ]);
        });
    }
};
