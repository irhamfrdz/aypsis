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
        Schema::table('pricelist_meratus', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->after('jenis_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_meratus', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }
};
