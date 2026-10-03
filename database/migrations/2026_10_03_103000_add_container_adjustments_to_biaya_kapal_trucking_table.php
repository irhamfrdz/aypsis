<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->json('container_adjustments')->nullable()->after('no_bl');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->dropColumn('container_adjustments');
        });
    }
};
