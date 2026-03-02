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
        Schema::table('pricelist_opp_opts', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('nama_barang');
            $table->string('lokasi')->nullable()->after('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_opp_opts', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'lokasi']);
        });
    }
};
