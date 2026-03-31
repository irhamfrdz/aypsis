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
        Schema::table('stock_amprahan_usages', function (Blueprint $table) {
            $table->string('lain_lain')->nullable()->after('alat_berat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_amprahan_usages', function (Blueprint $table) {
            $table->dropColumn('lain_lain');
        });
    }
};
