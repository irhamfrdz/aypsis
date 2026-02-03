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
        Schema::table('pranota_obs', function (Blueprint $col) {
            $col->string('nomor_accurate')->nullable()->after('tanggal_ob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_obs', function (Blueprint $col) {
            $col->dropColumn('nomor_accurate');
        });
    }
};
