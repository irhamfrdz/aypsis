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
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->decimal('biaya_admin', 15, 2)->default(0)->after('adjustment');
        });

        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->decimal('biaya_admin', 15, 2)->default(0)->after('adjustment');
        });

        Schema::table('biaya_kapal_tanto', function (Blueprint $table) {
            $table->decimal('biaya_admin', 15, 2)->default(0)->after('adjustment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->dropColumn('biaya_admin');
        });

        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->dropColumn('biaya_admin');
        });

        Schema::table('biaya_kapal_tanto', function (Blueprint $table) {
            $table->dropColumn('biaya_admin');
        });
    }
};
