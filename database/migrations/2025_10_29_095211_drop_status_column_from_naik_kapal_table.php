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
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'dimuat', 'selesai', 'batal'])->default('menunggu')->after('pelabuhan_tujuan');
        });
    }
};
