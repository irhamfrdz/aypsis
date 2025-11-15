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
            $table->string('nama_kapal')->nullable()->after('plat_nomor');
            $table->string('nomor_voyage')->nullable()->after('nama_kapal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->dropColumn(['nama_kapal', 'nomor_voyage']);
        });
    }
};
