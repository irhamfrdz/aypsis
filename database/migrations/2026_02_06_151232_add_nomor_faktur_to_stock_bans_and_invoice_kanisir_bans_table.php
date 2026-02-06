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
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->string('nomor_faktur')->nullable()->after('nomor_seri');
        });

        Schema::table('invoice_kanisir_bans', function (Blueprint $table) {
            $table->string('nomor_faktur')->nullable()->after('nomor_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropColumn('nomor_faktur');
        });

        Schema::table('invoice_kanisir_bans', function (Blueprint $table) {
            $table->dropColumn('nomor_faktur');
        });
    }
};
