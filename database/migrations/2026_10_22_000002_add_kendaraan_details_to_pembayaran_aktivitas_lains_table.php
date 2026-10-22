<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pembayaran_aktivitas_lains', 'kendaraan_details')) {
            Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
                $table->json('kendaraan_details')->nullable()->after('nomor_polisi_list');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembayaran_aktivitas_lains', 'kendaraan_details')) {
            Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
                $table->dropColumn('kendaraan_details');
            });
        }
    }
};
