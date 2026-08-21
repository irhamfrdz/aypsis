<?php

use App\Models\InvoiceAktivitasLain;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        InvoiceAktivitasLain::whereIn('nomor_invoice', [
            'IAL-08-26-000066',
            'IAL-08-26-000067',
            'IAL-08-26-000068',
        ])->update(['status' => 'draft']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
