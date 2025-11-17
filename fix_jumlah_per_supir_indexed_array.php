<?php

/**
 * Script untuk memperbaiki jumlah_per_supir yang masih indexed array
 * Run dengan: php fix_jumlah_per_supir_indexed_array.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranUangMuka;
use Illuminate\Support\Facades\DB;

echo "=== Fix jumlah_per_supir: Convert Indexed Array to Associative ===\n\n";

try {
    DB::beginTransaction();

    // Get all PembayaranUangMuka records
    $pembayaranList = PembayaranUangMuka::all();
    
    echo "Total records: " . $pembayaranList->count() . "\n";
    
    $fixedCount = 0;
    $skippedCount = 0;

    foreach ($pembayaranList as $pembayaran) {
        echo "\nProcessing ID: {$pembayaran->id} - {$pembayaran->nomor_pembayaran}\n";
        
        $supirIds = $pembayaran->supir_ids;
        $jumlahPerSupir = $pembayaran->jumlah_per_supir;
        
        // Decode if string
        if (is_string($supirIds)) {
            $supirIds = json_decode($supirIds, true);
        }
        if (is_string($jumlahPerSupir)) {
            $jumlahPerSupir = json_decode($jumlahPerSupir, true);
        }
        
        if (!is_array($supirIds) || !is_array($jumlahPerSupir)) {
            echo "  ⚠️  Invalid data format, skipping\n";
            $skippedCount++;
            continue;
        }
        
        echo "  supir_ids: " . json_encode($supirIds) . "\n";
        echo "  jumlah_per_supir: " . json_encode($jumlahPerSupir) . "\n";
        
        // Check if jumlah_per_supir is indexed array (has numeric sequential keys starting from 0)
        $keys = array_keys($jumlahPerSupir);
        $isIndexedArray = ($keys === array_keys($keys)); // True if keys are 0,1,2,3...
        
        if (!$isIndexedArray) {
            echo "  ✓ Already associative array, skipping\n";
            $skippedCount++;
            continue;
        }
        
        // Convert indexed array to associative with supir IDs as keys
        $newJumlahPerSupir = [];
        foreach ($supirIds as $index => $supirId) {
            if (isset($jumlahPerSupir[$index])) {
                $newJumlahPerSupir[(string)$supirId] = $jumlahPerSupir[$index];
                echo "    Mapping index {$index} -> supir_id {$supirId} = {$jumlahPerSupir[$index]}\n";
            }
        }
        
        echo "  New jumlah_per_supir: " . json_encode($newJumlahPerSupir) . "\n";
        
        // Update
        $pembayaran->jumlah_per_supir = $newJumlahPerSupir;
        $pembayaran->save();
        
        echo "  ✅ FIXED!\n";
        $fixedCount++;
    }

    DB::commit();
    
    echo "\n=== SUMMARY ===\n";
    echo "Total records: " . $pembayaranList->count() . "\n";
    echo "Fixed: {$fixedCount}\n";
    echo "Skipped (already OK): {$skippedCount}\n";
    echo "\n✅ Done!\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
