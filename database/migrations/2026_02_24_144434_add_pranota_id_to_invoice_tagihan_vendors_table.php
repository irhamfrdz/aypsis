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
        Schema::table('invoice_tagihan_vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('pranota_invoice_vendor_supir_id')->nullable()->after('vendor_id');
            $table->foreign('pranota_invoice_vendor_supir_id', 'fk_invoice_pranota_vendor')->references('id')->on('pranota_invoice_vendor_supirs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_tagihan_vendors', function (Blueprint $table) {
            $table->dropForeign('fk_invoice_pranota_vendor');
            $table->dropColumn('pranota_invoice_vendor_supir_id');
        });
    }
};
