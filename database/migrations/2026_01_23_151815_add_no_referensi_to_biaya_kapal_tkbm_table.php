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
        Schema::table('biaya_kapal_tkbm', function (Blueprint $table) {
            $table->string('no_referensi')->nullable()->after('voyage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_tkbm', function (Blueprint $table) {
            $table->dropColumn('no_referensi');
        });
    }
};
