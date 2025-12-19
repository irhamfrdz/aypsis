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
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            // Add invoice_ids column to store comma-separated invoice IDs
            $table->text('invoice_ids')->nullable()->after('akun_bank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->dropColumn('invoice_ids');
        });
    }
};
