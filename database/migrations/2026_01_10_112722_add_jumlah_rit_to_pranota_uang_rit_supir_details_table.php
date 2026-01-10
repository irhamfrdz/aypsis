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
            $table->integer('jumlah_rit')->default(0)->after('supir_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rit_supir_details', function (Blueprint $table) {
            $table->dropColumn('jumlah_rit');
        });
    }
};
