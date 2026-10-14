<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            foreach ([
                'invoice_kontainer_sewa_items',
                'pembayaran_pranota_kontainer_items',
                'pembayaran_pranota_kontainer',
                'pembayaran_pranota_tagihan_kontainer_tagihan',
                'pembayaran_pranota_tagihan_kontainer',
                'invoices_kontainer_sewa',
                'pranota_tagihan_kontainer_sewa',
                'pranota_tagihan_kontainers',
                'pranota_tagihan_kontainer',
                'daftar_tagihan_kontainer_sewa_dua',
                'daftar_tagihan_kontainer_sewa',
                'tagihan_kontainer_sewa_kontainers',
                'tagihan_kontainer_sewa',
            ] as $table) {
                Schema::dropIfExists($table);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        if (Schema::hasTable('permissions')) {
            DB::table('permissions')
                ->where(function ($query) {
                    $query->where('name', 'like', 'tagihan-kontainer-sewa-%')
                        ->orWhere('name', 'like', 'pranota-kontainer-sewa-%')
                        ->orWhere('name', 'like', 'pembayaran-pranota-kontainer-%');
                })
                ->delete();
        }
    }

    public function down(): void
    {
        // These tables contain operational billing records and cannot be safely restored empty.
    }
};
