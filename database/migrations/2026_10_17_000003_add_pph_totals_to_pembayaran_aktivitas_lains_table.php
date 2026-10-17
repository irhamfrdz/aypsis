<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->decimal('pph_nominal', 15, 2)->default(0)->after('pph_persen');
            $table->decimal('grand_total', 15, 2)->default(0)->after('jumlah');
        });

        DB::table('pembayaran_aktivitas_lains')->update([
            'pph_nominal' => DB::raw("CASE WHEN jenis_aktivitas = 'Pembayaran Kendaraan' THEN ROUND(jumlah * COALESCE(pph_persen, 0) / 100, 0) ELSE 0 END"),
        ]);
        DB::table('pembayaran_aktivitas_lains')->update([
            'grand_total' => DB::raw('jumlah - pph_nominal'),
        ]);
    }

    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->dropColumn(['pph_nominal', 'grand_total']);
        });
    }
};
