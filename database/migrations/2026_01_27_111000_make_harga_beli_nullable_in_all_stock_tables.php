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
        $tables = ['stock_bans', 'stock_ring_velgs', 'stock_velgs', 'stock_ban_dalams'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('harga_beli', 15, 2)->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['stock_bans', 'stock_ring_velgs', 'stock_velgs', 'stock_ban_dalams'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('harga_beli', 15, 2)->default(0)->change();
                });
            }
        }
    }
};
