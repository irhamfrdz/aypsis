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
        Schema::table('pranota_obs', function (Blueprint $table) {
            $table->date('tanggal_ob')->nullable()->after('no_voyage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_obs', function (Blueprint $table) {
            $table->dropColumn('tanggal_ob');
        });
    }
};
