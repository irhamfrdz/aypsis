<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update kolom meter_kubik, panjang, lebar, tinggi, tonase dari decimal(x,6) ke decimal(x,3)
     */
    public function up(): void
    {
        // Update tanda_terimas table - dimension columns to 3 decimal places
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN panjang DECIMAL(8,3) NULL COMMENT 'Length in meters with 3 decimal places'");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN lebar DECIMAL(8,3) NULL COMMENT 'Width in meters with 3 decimal places'");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN tinggi DECIMAL(8,3) NULL COMMENT 'Height in meters with 3 decimal places'");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN meter_kubik DECIMAL(12,3) NULL COMMENT 'Volume in m³ with 3 decimal places'");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN tonase DECIMAL(10,3) NULL COMMENT 'Weight in tons with 3 decimal places'");
        
        echo "Updated tanda_terimas columns to DECIMAL(x,3)\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 6 decimal places
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN panjang DECIMAL(8,2) NULL");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN lebar DECIMAL(8,2) NULL");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN tinggi DECIMAL(8,2) NULL");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN meter_kubik DECIMAL(12,6) NULL");
        DB::statement("ALTER TABLE tanda_terimas MODIFY COLUMN tonase DECIMAL(10,2) NULL");
        
        echo "Reverted tanda_terimas columns to original precision\n";
    }
};
