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
     * Perbaikan: Migration sebelumnya salah konversi volume.
     * Recalculate volume berdasarkan dimensi yang sudah benar.
     */
    public function up(): void
    {
        // Recalculate semua volume berdasarkan dimensi yang ada
        // Ini lebih aman daripada asumsi kalkulasi balik
        DB::statement("
            UPDATE tanda_terima_lcl_items 
            SET meter_kubik = panjang * lebar * tinggi
            WHERE panjang IS NOT NULL 
                AND lebar IS NOT NULL 
                AND tinggi IS NOT NULL
                AND panjang > 0 
                AND lebar > 0 
                AND tinggi > 0
        ");
        
        echo 'Volume recalculated for all LCL items based on current dimensions.';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - recalculation is always correct
        echo 'No rollback needed for volume recalculation.';
    }
};
