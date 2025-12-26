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
        // Check if pembayaran_dp_obs table exists, otherwise use realisasi_uang_muka
        $tableName = Schema::hasTable('pembayaran_dp_obs') ? 'pembayaran_dp_obs' : 'realisasi_uang_muka';
        
        Schema::table($tableName, function (Blueprint $table) {
            $table->string('nomor_accurate')->nullable()->after('nomor_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if pembayaran_dp_obs table exists, otherwise use realisasi_uang_muka
        $tableName = Schema::hasTable('pembayaran_dp_obs') ? 'pembayaran_dp_obs' : 'realisasi_uang_muka';
        
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('nomor_accurate');
        });
    }
};
