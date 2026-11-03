<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pembayaran_pranota_uang_jalan_akun_items', 'pranota_uang_jalan_id')) {
            Schema::table('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) {
                $table->unsignedBigInteger('pranota_uang_jalan_id')->nullable()->after('akun_coa_id');
            });
        }

        Schema::table('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) {
            $table->foreign('pranota_uang_jalan_id', 'fk_ppuj_akun_pranota')
                ->references('id')->on('pranota_uang_jalans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $sqlite = Schema::getConnection()->getDriverName() === 'sqlite';
        Schema::table('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) use ($sqlite) {
            $table->dropForeign($sqlite ? ['pranota_uang_jalan_id'] : 'fk_ppuj_akun_pranota');
            $table->dropColumn('pranota_uang_jalan_id');
        });
    }
};
