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
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->date('tanggal_digunakan')->nullable()->after('tanggal_keluar');
        });

        Schema::table('stock_ban_dalam_usages', function (Blueprint $table) {
            $table->date('tanggal_digunakan')->nullable()->after('tanggal_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropColumn('tanggal_digunakan');
        });

        Schema::table('stock_ban_dalam_usages', function (Blueprint $table) {
            $table->dropColumn('tanggal_digunakan');
        });
    }
};
