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
        Schema::table('biaya_kapal_air', function (Blueprint $table) {
            $table->boolean('is_lumpsum')->default(false)->after('type_keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_air', function (Blueprint $table) {
            $table->dropColumn('is_lumpsum');
        });
    }
};
