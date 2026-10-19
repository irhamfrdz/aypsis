<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->string('status_kontainer', 10)->nullable()->after('massa');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_opp_opts', function (Blueprint $table) {
            $table->dropColumn('status_kontainer');
        });
    }
};
