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
        Schema::table('kwitansi_details', function (Blueprint $table) {
            $table->dropColumn(['dept', 'proyek', 'sn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kwitansi_details', function (Blueprint $table) {
            $table->string('dept')->nullable();
            $table->string('proyek')->nullable();
            $table->string('sn')->nullable();
        });
    }
};
