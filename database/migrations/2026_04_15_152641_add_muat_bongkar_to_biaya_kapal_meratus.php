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
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->boolean('is_muat')->default(false)->after('size');
            $table->boolean('is_bongkar')->default(false)->after('is_muat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->dropColumn(['is_muat', 'is_bongkar']);
        });
    }
};
