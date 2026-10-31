<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nama_stock_bans', function (Blueprint $table) {
            $table->unsignedInteger('kuantitas')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('nama_stock_bans', function (Blueprint $table) {
            $table->dropColumn('kuantitas');
        });
    }
};
