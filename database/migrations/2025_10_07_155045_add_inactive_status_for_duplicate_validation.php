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
        // Cek duplikasi nomor kontainer antara stock_kontainers dan kontainers
        $duplicates = DB::select("
            SELECT sk.nomor_seri_gabungan 
            FROM stock_kontainers sk 
            INNER JOIN kontainers k ON sk.nomor_seri_gabungan = k.nomor_seri_gabungan
            WHERE sk.nomor_seri_gabungan IS NOT NULL 
            AND k.nomor_seri_gabungan IS NOT NULL
        ");

        if (!empty($duplicates)) {
            echo "Ditemukan " . count($duplicates) . " nomor kontainer yang duplikat antara stock_kontainers dan kontainers:\n";
            
            foreach ($duplicates as $duplicate) {
                echo "- " . $duplicate->nomor_seri_gabungan . "\n";
                
                // Set status stock_kontainer menjadi 'inactive' untuk duplikat
                DB::table('stock_kontainers')
                    ->where('nomor_seri_gabungan', $duplicate->nomor_seri_gabungan)
                    ->update(['status' => 'inactive']);
                    
                echo "  → Stock kontainer set to inactive\n";
            }
        } else {
            echo "Tidak ada duplikasi nomor kontainer yang ditemukan.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan status yang di-set menjadi inactive kembali ke available
        DB::table('stock_kontainers')
            ->where('status', 'inactive')
            ->update(['status' => 'available']);
    }
};
