<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->index('biaya_kapal_id', 'biaya_kapal_pelindos_biaya_kapal_id_index');
        });

        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->dropUnique('biaya_kapal_pelindos_biaya_kapal_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->unique('biaya_kapal_id', 'biaya_kapal_pelindos_biaya_kapal_id_unique');
        });

        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->dropIndex('biaya_kapal_pelindos_biaya_kapal_id_index');
        });
    }
};
