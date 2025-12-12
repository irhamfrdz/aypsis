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
            $table->string('kegiatan')->nullable()->after('jenis_transaksi');
            $table->decimal('dp_amount', 15, 2)->nullable()->after('uang_muka_amount')->comment('Alias untuk uang_muka_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            $table->dropColumn(['kegiatan', 'dp_amount']);
        });
    }
};
