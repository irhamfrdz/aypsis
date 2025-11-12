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
        Schema::table('kelola_bbm', function (Blueprint $table) {
            $table->integer('bulan')->after('tanggal'); // 1-12
            $table->integer('tahun')->after('bulan'); // e.g., 2025
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelola_bbm', function (Blueprint $table) {
            $table->dropColumn(['bulan', 'tahun']);
        });
    }
};
