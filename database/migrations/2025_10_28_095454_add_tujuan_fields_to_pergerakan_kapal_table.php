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
        Schema::table('pergerakan_kapal', function (Blueprint $table) {
            $table->string('tujuan_asal')->nullable()->after('pelabuhan_transit');
            $table->string('tujuan_tujuan')->nullable()->after('tujuan_asal');
            $table->string('tujuan_transit')->nullable()->after('tujuan_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pergerakan_kapal', function (Blueprint $table) {
            $table->dropColumn(['tujuan_asal', 'tujuan_tujuan', 'tujuan_transit']);
        });
    }
};
