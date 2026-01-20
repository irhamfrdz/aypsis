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
        Schema::table('biaya_kapal_air', function (Blueprint $table) {
            $table->decimal('biaya_agen', 15, 2)->default(0)->after('jasa_air');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_air', function (Blueprint $table) {
            $table->dropColumn('biaya_agen');
        });
    }
};
