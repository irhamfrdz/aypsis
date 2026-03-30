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
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->string('status_pranota')->default('Belum')->after('type_amprahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->dropColumn('status_pranota');
        });
    }
};
