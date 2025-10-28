<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update kolom total_volume dan total_ton dari decimal(x,6) ke decimal(x,3)
     */
    public function up(): void
    {
        // Update prospek table - volume and weight columns to 3 decimal places
        DB::statement("ALTER TABLE prospek MODIFY COLUMN total_volume DECIMAL(12,3) NULL COMMENT 'Total volume in m³ with 3 decimal places'");
        DB::statement("ALTER TABLE prospek MODIFY COLUMN total_ton DECIMAL(10,3) NULL COMMENT 'Total weight in tons with 3 decimal places'");
        
        echo "Updated prospek columns to DECIMAL(x,3)\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 6 decimal places (original format)
        DB::statement("ALTER TABLE prospek MODIFY COLUMN total_volume DECIMAL(12,6) NULL");
        DB::statement("ALTER TABLE prospek MODIFY COLUMN total_ton DECIMAL(10,6) NULL");
        
        echo "Reverted prospek columns to DECIMAL(x,6)\n";
    }
};
