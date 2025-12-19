<?php

/**
 * Script untuk update status prospek menjadi 'sudah_muat' 
 * berdasarkan nomor kontainer yang ada di table bls
 * 
 * Cara menjalankan:
 * php update_prospek_status_from_bl.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;
use App\Models\Prospek;
use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "Update Status Prospek Berdasarkan Table BLs\n";
echo "==============================================\n\n";

try {
    // Get all unique container numbers from BLs (exclude 'cargo')
    $containerNumbers = Bl::whereNotNull('nomor_kontainer')
        ->where('nomor_kontainer', '!=', '')
        ->where('nomor_kontainer', '!=', 'cargo')
        ->distinct()
        ->pluck('nomor_kontainer')
        ->toArray();
    
    echo "Total kontainer unik di table BLs: " . count($containerNumbers) . "\n\n";
    
    if (empty($containerNumbers)) {
        echo "Tidak ada kontainer untuk diproses.\n";
        exit(0);
    }
    
    // Get prospek records that match these container numbers and not yet 'sudah_muat'
    $prospeksToUpdate = Prospek::whereIn('nomor_kontainer', $containerNumbers)
        ->where('status', '!=', Prospek::STATUS_SUDAH_MUAT)
        ->get();
    
    echo "Prospek yang akan diupdate: " . $prospeksToUpdate->count() . "\n\n";
    
    if ($prospeksToUpdate->isEmpty()) {
        echo "Semua prospek sudah berstatus 'sudah_muat' atau tidak ada yang cocok.\n";
        exit(0);
    }
    
    // Show details before update
    echo "Detail Prospek yang akan diupdate:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-20s %-15s %-20s\n", "ID", "Nomor Kontainer", "Status Lama", "Kapal");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($prospeksToUpdate as $prospek) {
        printf("%-5s %-20s %-15s %-20s\n", 
            $prospek->id, 
            $prospek->nomor_kontainer, 
            $prospek->status,
            $prospek->nama_kapal ?? '-'
        );
    }
    echo str_repeat("-", 80) . "\n\n";
    
    // Ask for confirmation
    echo "Apakah Anda yakin ingin mengupdate status prospek ini menjadi 'sudah_muat'? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);
    
    if ($confirmation !== 'yes' && $confirmation !== 'y') {
        echo "\nUpdate dibatalkan.\n";
        exit(0);
    }
    
    echo "\nMemulai update...\n\n";
    
    // Start transaction
    DB::beginTransaction();
    
    try {
        $updatedCount = 0;
        
        foreach ($prospeksToUpdate as $prospek) {
            $prospek->status = Prospek::STATUS_SUDAH_MUAT;
            $prospek->updated_by = 1; // Admin user ID
            $prospek->save();
            
            $updatedCount++;
            echo "✓ Updated Prospek ID {$prospek->id} - Kontainer: {$prospek->nomor_kontainer}\n";
        }
        
        DB::commit();
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "SUCCESS!\n";
        echo "Total prospek yang berhasil diupdate: {$updatedCount}\n";
        echo str_repeat("=", 80) . "\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    
} catch (\Exception $e) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "ERROR!\n";
    echo "Pesan error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo str_repeat("=", 80) . "\n";
    exit(1);
}
