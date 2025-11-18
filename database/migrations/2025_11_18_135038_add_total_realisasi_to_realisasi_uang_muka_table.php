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
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            $table->decimal('total_realisasi', 15, 2)->after('keterangan_per_supir')->default(0)->comment('Total realisasi asli sebelum dikurangi DP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            $table->dropColumn('total_realisasi');
        });
    }
};
