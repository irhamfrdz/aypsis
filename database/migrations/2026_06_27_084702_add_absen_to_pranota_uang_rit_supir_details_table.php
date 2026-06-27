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
        Schema::table('pranota_uang_rit_supir_details', function (Blueprint $table) {
            $table->integer('absen')->default(0)->after('jumlah_rit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rit_supir_details', function (Blueprint $table) {
            $table->dropColumn('absen');
        });
    }
};
