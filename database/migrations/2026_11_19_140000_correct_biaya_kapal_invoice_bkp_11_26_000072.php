<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameInvoice('BKP-11-26-000072', 'BKP-11-26-000070');
    }

    public function down(): void
    {
        $this->renameInvoice('BKP-11-26-000070', 'BKP-11-26-000072');
    }

    private function renameInvoice(string $from, string $to): void
    {
        if (! DB::table('biaya_kapals')->where('nomor_invoice', $from)->exists()) {
            return;
        }

        if (DB::table('biaya_kapals')->where('nomor_invoice', $to)->exists()) {
            throw new RuntimeException("Nomor invoice {$to} sudah digunakan.");
        }

        DB::table('biaya_kapals')
            ->where('nomor_invoice', $from)
            ->update(['nomor_invoice' => $to]);
    }
};
