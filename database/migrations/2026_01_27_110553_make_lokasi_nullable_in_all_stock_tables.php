<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL for safety if doctrine/dbal is missing, or standard schema builder
        // Since we are not sure about dbal, we can try Schema builder first. 
        // But if it fails, the user will have errors.
        // Let's stick to standard Schema builder, assuming environment is set up.
        
        $tables = ['stock_bans', 'stock_ring_velgs', 'stock_velgs', 'stock_ban_dalams'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('lokasi')->nullable()->change();
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
                    // Reverting to not nullable with default
                    $table->string('lokasi')->default('Gudang Utama')->nullable(false)->change();
                });
            }
        }
    }
};
