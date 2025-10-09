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
            $table->decimal('subtotal_pembayaran', 15, 2)->after('jumlah_per_supir')->comment('Subtotal sebelum dikurangi DP');
            $table->decimal('dp_amount', 15, 2)->default(0)->after('subtotal_pembayaran')->comment('Jumlah DP yang digunakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            $table->dropColumn(['subtotal_pembayaran', 'dp_amount']);
        });
    }
};
