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
            $table->foreignId('pembayaran_dp_ob_id')->nullable()->after('keterangan')->constrained('pembayaran_dp_obs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_dp_ob_id']);
            $table->dropColumn('pembayaran_dp_ob_id');
        });
    }
};
