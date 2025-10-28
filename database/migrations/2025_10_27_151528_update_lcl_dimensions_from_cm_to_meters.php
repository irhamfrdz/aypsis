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
        // Konversi data LCL items dari cm ke meter
        // Asumsi: data lama disimpan dalam cm, perlu dikonversi ke meter
        
        // Update tanda_terima_lcl_items - konversi dari cm ke meter
        DB::statement("
            UPDATE tanda_terima_lcl_items 
            SET 
                panjang = CASE 
                    WHEN panjang IS NOT NULL AND panjang > 10 THEN panjang / 100 
                    ELSE panjang 
                END,
                lebar = CASE 
                    WHEN lebar IS NOT NULL AND lebar > 10 THEN lebar / 100 
                    ELSE lebar 
                END,
                tinggi = CASE 
                    WHEN tinggi IS NOT NULL AND tinggi > 10 THEN tinggi / 100 
                    ELSE tinggi 
                END,
                meter_kubik = CASE 
                    WHEN panjang IS NOT NULL AND lebar IS NOT NULL AND tinggi IS NOT NULL 
                         AND panjang > 10 AND lebar > 10 AND tinggi > 10 
                    THEN (panjang / 100) * (lebar / 100) * (tinggi / 100)
                    WHEN panjang IS NOT NULL AND lebar IS NOT NULL AND tinggi IS NOT NULL 
                         AND (panjang <= 10 OR lebar <= 10 OR tinggi <= 10)
                    THEN panjang * lebar * tinggi
                    ELSE meter_kubik 
                END
        ");

        // Update comments untuk menunjukkan bahwa sekarang dalam meter
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN panjang DECIMAL(10,2) NULL COMMENT 'Length in meters'");
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN lebar DECIMAL(10,2) NULL COMMENT 'Width in meters'");
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN tinggi DECIMAL(10,2) NULL COMMENT 'Height in meters'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan dari meter ke cm (jika diperlukan rollback)
        DB::statement("
            UPDATE tanda_terima_lcl_items 
            SET 
                panjang = CASE 
                    WHEN panjang IS NOT NULL AND panjang <= 10 THEN panjang * 100 
                    ELSE panjang 
                END,
                lebar = CASE 
                    WHEN lebar IS NOT NULL AND lebar <= 10 THEN lebar * 100 
                    ELSE lebar 
                END,
                tinggi = CASE 
                    WHEN tinggi IS NOT NULL AND tinggi <= 10 THEN tinggi * 100 
                    ELSE tinggi 
                END
        ");

        // Kembalikan comment ke cm
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN panjang DECIMAL(10,2) NULL COMMENT 'Length in cm'");
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN lebar DECIMAL(10,2) NULL COMMENT 'Width in cm'");
        DB::statement("ALTER TABLE tanda_terima_lcl_items MODIFY COLUMN tinggi DECIMAL(10,2) NULL COMMENT 'Height in cm'");
    }
};
