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
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->dropColumn(['biaya_insa', 'biaya_pbni', 'pph']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->decimal('biaya_insa', 12, 2)->default(0)->after('lokasi');
            $table->decimal('biaya_pbni', 12, 2)->default(0)->after('biaya_insa');
            $table->decimal('pph', 12, 2)->default(0)->after('sub_total');
        });
    }
};
