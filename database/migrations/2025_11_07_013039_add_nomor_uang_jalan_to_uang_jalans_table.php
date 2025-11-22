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
            // Add columns only if they don't exist
            if (!Schema::hasColumn('uang_jalans', 'nomor_kas_bank')) {
                $table->string('nomor_kas_bank', 50)->nullable()->after('nomor_uang_jalan');
            }
            if (!Schema::hasColumn('uang_jalans', 'tanggal_kas_bank')) {
                $table->date('tanggal_kas_bank')->nullable()->after('nomor_kas_bank');
            }
            if (!Schema::hasColumn('uang_jalans', 'kegiatan_bongkar_muat')) {
                $table->enum('kegiatan_bongkar_muat', ['bongkar', 'muat'])->nullable()->after('tanggal_kas_bank');
            }
            if (!Schema::hasColumn('uang_jalans', 'jenis_transaksi')) {
                $table->enum('jenis_transaksi', ['debit', 'kredit'])->nullable()->after('kegiatan_bongkar_muat');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_uang_jalan')) {
                $table->decimal('jumlah_uang_jalan', 12, 2)->default(0)->after('jenis_transaksi');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_mel')) {
                $table->decimal('jumlah_mel', 12, 2)->default(0)->after('jumlah_uang_jalan');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_pelancar')) {
                $table->decimal('jumlah_pelancar', 12, 2)->default(0)->after('jumlah_mel');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_kawalan')) {
                $table->decimal('jumlah_kawalan', 12, 2)->default(0)->after('jumlah_pelancar');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_parkir')) {
                $table->decimal('jumlah_parkir', 12, 2)->default(0)->after('jumlah_kawalan');
            }
            if (!Schema::hasColumn('uang_jalans', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('jumlah_parkir');
            }
            if (!Schema::hasColumn('uang_jalans', 'alasan_penyesuaian')) {
                $table->string('alasan_penyesuaian')->nullable()->after('subtotal');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_penyesuaian')) {
                $table->decimal('jumlah_penyesuaian', 12, 2)->default(0)->after('alasan_penyesuaian');
            }
            if (!Schema::hasColumn('uang_jalans', 'jumlah_total')) {
                $table->decimal('jumlah_total', 12, 2)->default(0)->after('jumlah_penyesuaian');
            }
            if (!Schema::hasColumn('uang_jalans', 'memo')) {
                $table->text('memo')->nullable()->after('jumlah_total');
            }
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
