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
            $table->unsignedBigInteger('nama_stock_ban_id')->nullable()->after('id');
            $table->foreign('nama_stock_ban_id')->references('id')->on('nama_stock_bans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_bans', function (Blueprint $table) {
            $table->dropForeign(['nama_stock_ban_id']);
            $table->dropColumn('nama_stock_ban_id');
        });
    }
};
