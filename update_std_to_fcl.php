<?php

/**
 * Script untuk mengubah tipe kontainer dari "STD" menjadi "FCL"
 * pada tabel surat_jalans
 * 
 * Jalankan script ini dengan perintah: php update_std_to_fcl.php
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Script Update Tipe Kontainer STD ke FCL ===\n";
    echo "Mengubah 'STD' menjadi 'FCL' pada tabel surat_jalans\n\n";
    
    // Cek jumlah data yang akan diubah
    $count = DB::table('surat_jalans')
        ->where('tipe_kontainer', 'STD')
        ->count();
    
    echo "Ditemukan {$count} record dengan tipe kontainer 'STD'\n";
    
    if ($count > 0) {
        // Konfirmasi sebelum update
        echo "Apakah Anda ingin melanjutkan update? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $confirmation = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($confirmation) === 'y' || strtolower($confirmation) === 'yes') {
            // Lakukan update
            $updated = DB::table('surat_jalans')
                ->where('tipe_kontainer', 'STD')
                ->update(['tipe_kontainer' => 'FCL']);
            
            echo "\n✅ Berhasil mengubah {$updated} record dari 'STD' ke 'FCL'\n";
            
            // Verifikasi hasil update
            $remainingCount = DB::table('surat_jalans')
                ->where('tipe_kontainer', 'STD')
                ->count();
            
            $fclCount = DB::table('surat_jalans')
                ->where('tipe_kontainer', 'FCL')
                ->count();
            
            echo "\nVerifikasi hasil:\n";
            echo "- Record dengan 'STD': {$remainingCount}\n";
            echo "- Record dengan 'FCL': {$fclCount}\n";
            
        } else {
            echo "\n❌ Update dibatalkan\n";
        }
    } else {
        echo "\n✅ Tidak ada data yang perlu diubah\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Script selesai ===\n";