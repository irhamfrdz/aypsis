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
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->string('nomor_uang_jalan', 50)->unique()->after('id');
            $table->string('nomor_kas_bank', 50)->nullable()->after('nomor_uang_jalan');
            $table->date('tanggal_kas_bank')->nullable()->after('nomor_kas_bank');
            $table->enum('kegiatan_bongkar_muat', ['bongkar', 'muat'])->nullable()->after('tanggal_kas_bank');
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->nullable()->after('kegiatan_bongkar_muat');
            $table->enum('kategori_uang_jalan', ['uang_jalan', 'non_uang_jalan'])->nullable()->after('jenis_transaksi');
            $table->decimal('jumlah_uang_jalan', 12, 2)->default(0)->after('kategori_uang_jalan');
            $table->decimal('jumlah_mel', 12, 2)->default(0)->after('jumlah_uang_jalan');
            $table->decimal('jumlah_pelancar', 12, 2)->default(0)->after('jumlah_mel');
            $table->decimal('jumlah_kawalan', 12, 2)->default(0)->after('jumlah_pelancar');
            $table->decimal('jumlah_parkir', 12, 2)->default(0)->after('jumlah_kawalan');
            $table->decimal('subtotal', 12, 2)->default(0)->after('jumlah_parkir');
            $table->string('alasan_penyesuaian')->nullable()->after('subtotal');
            $table->decimal('jumlah_penyesuaian', 12, 2)->default(0)->after('alasan_penyesuaian');
            $table->decimal('jumlah_total', 12, 2)->default(0)->after('jumlah_penyesuaian');
            $table->text('memo')->nullable()->after('jumlah_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_uang_jalan',
                'nomor_kas_bank',
                'tanggal_kas_bank',
                'kegiatan_bongkar_muat',
                'jenis_transaksi',
                'kategori_uang_jalan',
                'jumlah_uang_jalan',
                'jumlah_mel',
                'jumlah_pelancar',
                'jumlah_kawalan',
                'jumlah_parkir',
                'subtotal',
                'alasan_penyesuaian',
                'jumlah_penyesuaian',
                'jumlah_total',
                'memo'
            ]);
        });
    }
};
