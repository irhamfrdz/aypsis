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
            $table->dropColumn(['pelabuhan_asal', 'pelabuhan_tujuan', 'pelabuhan_transit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pergerakan_kapal', function (Blueprint $table) {
            $table->string('pelabuhan_asal')->nullable()->after('transit');
            $table->string('pelabuhan_tujuan')->nullable()->after('pelabuhan_asal');
            $table->string('pelabuhan_transit')->nullable()->after('pelabuhan_tujuan');
        });
    }
};
