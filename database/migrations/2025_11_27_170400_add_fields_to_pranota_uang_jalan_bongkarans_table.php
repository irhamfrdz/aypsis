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
        Schema::table('pranota_uang_jalan_bongkarans', function (Blueprint $table) {
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'nomor_pranota')) {
                $table->string('nomor_pranota', 50)->unique()->after('id');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'tanggal_pranota')) {
                $table->date('tanggal_pranota')->after('nomor_pranota');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'periode_tagihan')) {
                $table->string('periode_tagihan', 20)->after('tanggal_pranota');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'jumlah_uang_jalan_bongkaran')) {
                $table->integer('jumlah_uang_jalan_bongkaran')->default(0)->after('periode_tagihan');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('jumlah_uang_jalan_bongkaran');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'penyesuaian')) {
                $table->decimal('penyesuaian', 15, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'keterangan_penyesuaian')) {
                $table->text('keterangan_penyesuaian')->nullable()->after('penyesuaian');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['unpaid', 'paid', 'partial', 'cancelled'])->default('unpaid')->after('keterangan_penyesuaian');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'catatan')) {
                $table->text('catatan')->nullable()->after('status_pembayaran');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'created_by')) {
                $table->unsignedBigInteger('created_by')->after('catatan');
            }
            if (!Schema::hasColumn('pranota_uang_jalan_bongkarans', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }

            // Indexes
            $table->index('nomor_pranota');
            $table->index('status_pembayaran');
            $table->index('periode_tagihan');
            $table->index('created_by');

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_jalan_bongkarans', function (Blueprint $table) {
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'keterangan_penyesuaian')) {
                $table->dropColumn('keterangan_penyesuaian');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'penyesuaian')) {
                $table->dropColumn('penyesuaian');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'jumlah_uang_jalan_bongkaran')) {
                $table->dropColumn('jumlah_uang_jalan_bongkaran');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'periode_tagihan')) {
                $table->dropColumn('periode_tagihan');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'tanggal_pranota')) {
                $table->dropColumn('tanggal_pranota');
            }
            if (Schema::hasColumn('pranota_uang_jalan_bongkarans', 'nomor_pranota')) {
                $table->dropColumn('nomor_pranota');
            }
        });
    }
};
