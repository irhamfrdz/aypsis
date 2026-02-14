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
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->unsignedBigInteger('alat_berat_id')->nullable()->after('mobil_id');
            $table->foreign('alat_berat_id')->references('id')->on('alat_berats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropForeign(['alat_berat_id']);
            $table->dropColumn('alat_berat_id');
        });
    }
};
