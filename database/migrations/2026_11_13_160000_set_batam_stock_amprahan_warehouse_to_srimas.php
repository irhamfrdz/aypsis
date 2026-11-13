<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stock_amprahans')
            ->where('lokasi_penyimpanan', 'KANTOR AYP BATAM')
            ->update(['gudang' => 'Gudang Kantor Srimas']);
    }

    public function down(): void
    {
        // Previous warehouse values cannot be recovered after this data update.
    }
};
