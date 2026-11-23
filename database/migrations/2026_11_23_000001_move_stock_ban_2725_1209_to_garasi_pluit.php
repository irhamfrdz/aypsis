<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stock_bans')
            ->where('nomor_seri', '2725/1209')
            ->where('status', 'Terpakai')
            ->where('lokasi', 'Ruko 10')
            ->update([
                'status' => 'Stok',
                'lokasi' => 'Garasi Pluit',
                'mobil_id' => null,
                'alat_berat_id' => null,
                'penerima_id' => null,
                'penerima_manual' => null,
                'kapal_id' => null,
                'tanggal_keluar' => null,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // The previous vehicle assignment cannot be safely restored after this correction.
    }
};
