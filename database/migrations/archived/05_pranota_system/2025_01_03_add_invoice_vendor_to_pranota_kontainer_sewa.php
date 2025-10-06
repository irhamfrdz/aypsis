<?php

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
        Schema::table('pranota_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('no_invoice_vendor')->nullable()->after('keterangan');
            $table->date('tgl_invoice_vendor')->nullable()->after('no_invoice_vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->dropColumn(['no_invoice_vendor', 'tgl_invoice_vendor']);
        });
    }
};