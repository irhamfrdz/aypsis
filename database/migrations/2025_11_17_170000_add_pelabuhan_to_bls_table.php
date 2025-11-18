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
        Schema::table('bls', function (Blueprint $table) {
            $table->string('pelabuhan_asal')->nullable()->after('no_voyage');
            $table->string('pelabuhan_tujuan')->nullable()->after('pelabuhan_asal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn(['pelabuhan_asal', 'pelabuhan_tujuan']);
        });
    }
};
