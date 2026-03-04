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
        Schema::table('master_pricelist_biaya_storages', function (Blueprint $table) {
            $table->dropColumn('free_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_biaya_storages', function (Blueprint $table) {
            $table->integer('free_time')->default(0)->after('biaya_per_hari');
        });
    }
};
