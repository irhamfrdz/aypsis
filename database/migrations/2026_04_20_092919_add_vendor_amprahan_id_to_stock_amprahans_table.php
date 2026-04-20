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
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_amprahan_id')->nullable()->after('nomor_bukti');
            $table->foreign('vendor_amprahan_id')->references('id')->on('vendor_amprahans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->dropForeign(['vendor_amprahan_id']);
            $table->dropColumn('vendor_amprahan_id');
        });
    }
};
