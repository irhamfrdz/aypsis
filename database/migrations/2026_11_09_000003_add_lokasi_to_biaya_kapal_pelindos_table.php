<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->after('jenis_tagihan');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }
};
