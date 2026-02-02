<?php

/**
 * Script to populate tanggal_ob column with created_at date values
 * Run this after migration: php update_tanggal_ob_from_created_at.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Starting to update tanggal_ob from created_at...\n\n";

try {
    // Get all pranota_obs records
    $pranotaObs = DB::table('pranota_obs')->get();
    
    echo "Found " . $pranotaObs->count() . " pranota OB records.\n\n";
    
    $updated = 0;
    
    foreach ($pranotaObs as $pranota) {
        // Extract date from created_at
        $tanggalOb = date('Y-m-d', strtotime($pranota->created_at));
        
        // Update tanggal_ob
        DB::table('pranota_obs')
            ->where('id', $pranota->id)
            ->update(['tanggal_ob' => $tanggalOb]);
        
        $updated++;
        echo "Updated ID {$pranota->id} - {$pranota->nomor_pranota}: tanggal_ob = {$tanggalOb}\n";
    }
    
    echo "\n✓ Successfully updated {$updated} records.\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
