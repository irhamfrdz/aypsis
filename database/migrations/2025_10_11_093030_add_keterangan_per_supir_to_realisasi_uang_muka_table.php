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
            $table->json('keterangan_per_supir')->nullable()->after('jumlah_per_supir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            $table->dropColumn('keterangan_per_supir');
        });
    }
};
