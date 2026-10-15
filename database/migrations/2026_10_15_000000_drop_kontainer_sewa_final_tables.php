<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'btm_sewa_payment_details',
            'btm_sewa_payments',
            'btm_sewa_permohonan_transfer_details',
            'btm_sewa_permohonan_transfers',
            'btm_sewa_audits',
            'btm_sewa_pranotas',
            'btm_sewa_transactions',
            'btm_sewa_rates',
            'btm_sewa_units',
            'btm_sewa_sizes',
            'btm_sewa_types',
            'btm_sewa_vendors',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->whereIn('name', [
                'kontainer-sewa-final-view',
                'kontainer-sewa-final-create',
                'kontainer-sewa-final-update',
                'kontainer-sewa-final-delete',
            ])->delete();
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // The removed billing data cannot be reconstructed after this migration.
    }
};
