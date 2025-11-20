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
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'nomor_accurate')) {
                $table->string('nomor_accurate', 50)->nullable()->after('tanggal_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->dropColumn('nomor_accurate');
        });
    }
};
