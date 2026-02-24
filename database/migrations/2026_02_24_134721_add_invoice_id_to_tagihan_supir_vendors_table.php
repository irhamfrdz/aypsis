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
        Schema::table('tagihan_supir_vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_tagihan_vendor_id')->nullable()->after('vendor_id');
            $table->foreign('invoice_tagihan_vendor_id')->references('id')->on('invoice_tagihan_vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_supir_vendors', function (Blueprint $table) {
            $table->dropForeign(['invoice_tagihan_vendor_id']);
            $table->dropColumn('invoice_tagihan_vendor_id');
        });
    }
};
