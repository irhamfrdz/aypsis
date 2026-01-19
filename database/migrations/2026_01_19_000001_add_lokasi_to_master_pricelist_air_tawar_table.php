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
        Schema::table('master_pricelist_air_tawar', function (Blueprint $table) {
            $table->string('lokasi')->default('Jakarta')->after('nama_agen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_air_tawar', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }
};