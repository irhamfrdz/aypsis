<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('biaya_kapal_trucking', 'container_dooring_dates')) {
            Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
                $table->json('container_dooring_dates')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('biaya_kapal_trucking', 'container_dooring_dates')) {
            Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
                $table->dropColumn('container_dooring_dates');
            });
        }
    }
};
