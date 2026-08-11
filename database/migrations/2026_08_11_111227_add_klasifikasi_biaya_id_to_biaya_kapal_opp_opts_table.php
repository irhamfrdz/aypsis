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
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->unsignedBigInteger('klasifikasi_biaya_id')->nullable()->after('biaya_kapal_id');
            $table->foreign('klasifikasi_biaya_id')->references('id')->on('klasifikasi_biayas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->dropForeign(['klasifikasi_biaya_id']);
            $table->dropColumn('klasifikasi_biaya_id');
        });
    }
};
