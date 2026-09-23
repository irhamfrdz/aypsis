<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->boolean('is_per_container')->nullable()->after('is_bongkar');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->dropColumn('is_per_container');
        });
    }
};
