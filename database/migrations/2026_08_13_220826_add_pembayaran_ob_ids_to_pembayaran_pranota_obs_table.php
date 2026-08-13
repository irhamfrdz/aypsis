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
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->json('pembayaran_ob_ids')->nullable()->after('pembayaran_ob_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_obs', function (Blueprint $table) {
            $table->dropColumn('pembayaran_ob_ids');
        });
    }
};
