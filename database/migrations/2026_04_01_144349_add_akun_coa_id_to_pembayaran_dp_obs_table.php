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
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_coa_id')->nullable()->after('kas_bank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            $table->dropColumn('akun_coa_id');
        });
    }
};
