<?php

/**
 * Script untuk mengkonversi string IDs menjadi integer IDs
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranUangMuka;
use Illuminate\Support\Facades\DB;

echo "=== Convert String IDs to Integer IDs ===\n\n";

try {
    DB::beginTransaction();

    $pembayaranList = PembayaranUangMuka::all();
    
    $fixedCount = 0;

    foreach ($pembayaranList as $pum) {
        echo "Processing ID {$pum->id}: {$pum->nomor_pembayaran}\n";
        
        $supirIds = $pum->supir_ids;
        $jumlahPerSupir = $pum->jumlah_per_supir;
        
        // Check if supir_ids contains strings
        $hasStringIds = false;
        if (is_array($supirIds)) {
            foreach ($supirIds as $id) {
                if (is_string($id) && is_numeric($id)) {
                    $hasStringIds = true;
                    break;
                }
            }
        }
        
        if ($hasStringIds) {
            echo "  Found string IDs, converting...\n";
            
            // Convert supir_ids to integers
            $newSupirIds = array_map('intval', $supirIds);
            
            // Convert jumlah_per_supir keys to integers
            $newJumlahPerSupir = [];
            if (is_array($jumlahPerSupir)) {
                foreach ($jumlahPerSupir as $key => $value) {
                    $intKey = (int) $key;
                    $newJumlahPerSupir[$intKey] = $value;
                }
            }
            
            echo "  Old supir_ids: " . json_encode($supirIds) . "\n";
            echo "  New supir_ids: " . json_encode($newSupirIds) . "\n";
            echo "  Old jumlah_per_supir: " . json_encode($jumlahPerSupir) . "\n";
            echo "  New jumlah_per_supir: " . json_encode($newJumlahPerSupir) . "\n";
            
            // Update
            $pum->supir_ids = $newSupirIds;
            $pum->jumlah_per_supir = $newJumlahPerSupir;
            $pum->save();
            
            echo "  ✅ FIXED!\n";
            $fixedCount++;
        } else {
            echo "  ✓ Already using integer IDs\n";
        }
        
        echo "\n";
    }

    DB::commit();
    
    echo "=== SUMMARY ===\n";
    echo "Fixed: {$fixedCount} records\n";
    echo "\n✅ Done!\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
