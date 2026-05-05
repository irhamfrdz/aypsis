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
        Schema::table('buruhs', function (Blueprint $table) {
            $table->string('nik')->after('nama')->nullable();
            $table->dropColumn(['telepon', 'jabatan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buruhs', function (Blueprint $table) {
            $table->dropColumn('nik');
            $table->string('telepon')->nullable();
            $table->string('jabatan')->nullable();
        });
    }
};
