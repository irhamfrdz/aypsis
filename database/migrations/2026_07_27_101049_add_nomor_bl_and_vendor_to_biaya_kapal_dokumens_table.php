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
        Schema::table('biaya_kapal_dokumens', function (Blueprint $table) {
            $table->string('nomor_bl')->nullable()->after('voyage');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('nomor_bl');
            
            $table->foreign('vendor_id')->references('id')->on('pricelist_biaya_dokumen')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_dokumens', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['nomor_bl', 'vendor_id']);
        });
    }
};
