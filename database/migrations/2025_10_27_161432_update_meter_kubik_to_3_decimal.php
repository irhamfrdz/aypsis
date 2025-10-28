<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update kolom meter_kubik dari decimal(15,6) ke decimal(10,3)
     */
    public function up(): void
    {
        // Ubah struktur kolom ke decimal(10,3) 
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN meter_kubik DECIMAL(10,3) NULL COMMENT 'Volume in m³ with 3 decimal places'");
        
        // Recalculate dan round semua volume ke 3 desimal
        DB::statement("
            UPDATE tanda_terima_lcl_items 
            SET meter_kubik = ROUND(panjang * lebar * tinggi, 3)
            WHERE panjang IS NOT NULL 
                AND lebar IS NOT NULL 
                AND tinggi IS NOT NULL
                AND panjang > 0 
                AND lebar > 0 
                AND tinggi > 0
        ");
        
        echo 'Updated meter_kubik to DECIMAL(10,3) and recalculated volumes.';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke decimal(15,6)
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN meter_kubik DECIMAL(15,6) NULL COMMENT 'Volume in m³'");
        
        echo 'Reverted meter_kubik to DECIMAL(15,6).';
    }
};
