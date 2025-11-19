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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'nama_kapal')) {
                $table->string('nama_kapal')->nullable()->after('plat_nomor');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'nomor_voyage')) {
                $table->string('nomor_voyage')->nullable()->after('nama_kapal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'nama_kapal')) {
                $table->dropColumn('nama_kapal');
            }
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'nomor_voyage')) {
                $table->dropColumn('nomor_voyage');
            }
        });
    }
};
