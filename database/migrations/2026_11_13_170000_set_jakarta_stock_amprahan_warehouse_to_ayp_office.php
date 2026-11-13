<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stock_amprahans')
            ->where('lokasi_penyimpanan', 'KANTOR AYP JAKARTA')
            ->update(['gudang' => 'Gudang Kantor AYP Jakarta']);
    }

    public function down(): void
    {
        // Nilai gudang sebelumnya tidak dapat dipulihkan setelah pembaruan ini.
    }
};
