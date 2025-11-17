<?php

/**
 * Script untuk mengubah supir_ids dari nama menjadi ID di tabel pembayaran_uang_muka
 * Run dengan: php fix_pembayaran_uang_muka_supir_names_to_ids.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranUangMuka;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

echo "=== Fix Pembayaran Uang Muka: Convert Supir Names to IDs ===\n\n";

try {
    DB::beginTransaction();

    // Get all PembayaranUangMuka records
    $pembayaranList = PembayaranUangMuka::all();
    
    echo "Total records: " . $pembayaranList->count() . "\n";
    
    $fixedCount = 0;
    $skippedCount = 0;
    $errorCount = 0;

    foreach ($pembayaranList as $pembayaran) {
        echo "\nProcessing ID: {$pembayaran->id} - {$pembayaran->nomor_pembayaran}\n";
        
        // Decode supir_ids - handle double encoding if needed
        $supirIds = $pembayaran->supir_ids;
        
        // Check if it's a string (double encoded)
        if (is_string($supirIds)) {
            $supirIds = json_decode($supirIds, true);
            if (is_string($supirIds)) {
                $supirIds = json_decode($supirIds, true);
            }
        }
        
        // Make sure it's an array
        if (!is_array($supirIds)) {
            echo "  ⚠️  supir_ids is not array, skipping\n";
            $skippedCount++;
            continue;
        }
        
        echo "  Current supir_ids: " . json_encode($supirIds) . "\n";
        
        // Check if first element is numeric (already ID) or string (name)
        if (empty($supirIds)) {
            echo "  ⚠️  Empty supir_ids, skipping\n";
            $skippedCount++;
            continue;
        }
        
        $firstElement = $supirIds[0];
        
        // If first element is numeric, assume all are IDs already
        if (is_numeric($firstElement)) {
            echo "  ✓ Already using IDs, skipping\n";
            $skippedCount++;
            continue;
        }
        
        // Convert names to IDs
        $newSupirIds = [];
        $newJumlahPerSupir = [];
        $jumlahPerSupir = $pembayaran->jumlah_per_supir;
        
        // Decode jumlah_per_supir if needed
        if (is_string($jumlahPerSupir)) {
            $jumlahPerSupir = json_decode($jumlahPerSupir, true);
            if (is_string($jumlahPerSupir)) {
                $jumlahPerSupir = json_decode($jumlahPerSupir, true);
            }
        }
        
        if (!is_array($jumlahPerSupir)) {
            $jumlahPerSupir = [];
        }
        
        echo "  Current jumlah_per_supir: " . json_encode($jumlahPerSupir) . "\n";
        
        foreach ($supirIds as $index => $nama) {
            // Find karyawan by nama_lengkap
            $supir = Karyawan::where('nama_lengkap', $nama)->first();
            
            if ($supir) {
                $newSupirIds[] = $supir->id;
                
                // Transfer jumlah from name key to id key
                // Handle both associative array (nama => jumlah) and indexed array
                if (isset($jumlahPerSupir[$nama])) {
                    // Associative array: {"nama" => jumlah}
                    $newJumlahPerSupir[$supir->id] = $jumlahPerSupir[$nama];
                } elseif (isset($jumlahPerSupir[$index])) {
                    // Indexed array: [jumlah1, jumlah2, ...]
                    $newJumlahPerSupir[$supir->id] = $jumlahPerSupir[$index];
                }
                
                echo "    ✓ Converted: {$nama} -> ID {$supir->id}\n";
            } else {
                echo "    ❌ NOT FOUND: {$nama}\n";
                $errorCount++;
            }
        }
        
        // Only update if we successfully converted at least one
        if (!empty($newSupirIds)) {
            echo "  New supir_ids: " . json_encode($newSupirIds) . "\n";
            echo "  New jumlah_per_supir: " . json_encode($newJumlahPerSupir) . "\n";
            
            // Update the record - let Laravel's array cast handle JSON encoding
            $pembayaran->supir_ids = $newSupirIds;
            $pembayaran->jumlah_per_supir = $newJumlahPerSupir;
            $pembayaran->save();
            
            echo "  ✅ FIXED!\n";
            $fixedCount++;
        } else {
            echo "  ❌ Could not convert any names to IDs\n";
            $errorCount++;
        }
    }

    DB::commit();
    
    echo "\n=== SUMMARY ===\n";
    echo "Total records: " . $pembayaranList->count() . "\n";
    echo "Fixed: {$fixedCount}\n";
    echo "Skipped (already OK): {$skippedCount}\n";
    echo "Errors: {$errorCount}\n";
    echo "\n✅ Done!\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
