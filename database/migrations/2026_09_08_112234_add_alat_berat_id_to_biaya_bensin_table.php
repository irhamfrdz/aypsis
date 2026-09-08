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
        Schema::table('biaya_bensin', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->nullable()->change();
            $table->unsignedBigInteger('alat_berat_id')->nullable()->after('mobil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_bensin', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->nullable(false)->change();
            $table->dropColumn('alat_berat_id');
        });
    }
};
