<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pembayaran_aktivitas_lains')
            ->whereIn('jenis_aktivitas', [
                'Pembayaran Kapal',
                'Pembayaran Adjusment Uang Jalan',
                'Pembayaran Adjustment Uang Jalan',
                'Pembayaran Lain Lain',
            ])
            ->where(function ($query) {
                $query->whereNull('lokasi')->orWhere('lokasi', '');
            })
            ->update(['lokasi' => 'jakarta']);
    }

    public function down(): void
    {
        // Nilai Jakarta yang diisi migrasi tidak dapat dibedakan dari pilihan pengguna.
    }
};
