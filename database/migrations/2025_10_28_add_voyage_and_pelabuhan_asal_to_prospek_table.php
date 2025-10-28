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
        Schema::table('prospek', function (Blueprint $table) {
            $table->string('no_voyage', 50)->nullable()->after('kapal_id');
            $table->string('pelabuhan_asal', 100)->nullable()->after('no_voyage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            $table->dropColumn(['no_voyage', 'pelabuhan_asal']);
        });
    }
};