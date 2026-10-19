<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->decimal('pph_rate', 4, 2)->default(0)->after('biaya');
            $table->decimal('total_biaya', 15, 2)->default(0)->after('pph_rate');
        });

        DB::table('tagihan_service_kendaraan_batams')->update(['total_biaya' => DB::raw('biaya')]);

        Schema::table('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->after('total_biaya');
        });

        Schema::table('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->dropColumn(['pph_rate', 'total_biaya']);
        });
    }
};
