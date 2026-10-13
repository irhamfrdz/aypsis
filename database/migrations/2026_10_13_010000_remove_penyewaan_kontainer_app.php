<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop child tables first, including remnants of the earlier module schema.
        foreach ([
            'sk_invoice_grup_tagihans',
            'sk_invoice_grups',
            'sk_tagihan_bulans',
            'sk_sewas',
            'sk_tarif_sewas',
            'sk_tarifs',
            'sk_kontainers',
            'sk_ukuran_kontainers',
            'sk_tipe_kontainers',
            'sk_ukurans',
            'sk_tipes',
            'sk_customers',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // Retired module: deleted rental records cannot be restored by rollback.
    }
};
