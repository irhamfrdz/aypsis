<?php

/**
 * Script to update vendor names in database
 * This script updates vendor names from full company names to short codes
 * 
 * Usage: php update_vendor_names.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=================================================\n";
echo "  UPDATE VENDOR NAMES SCRIPT\n";
echo "=================================================\n\n";

// Define vendor mapping: old name => new name
$vendorMapping = [
    'PT. DEPO PETIKEMAS EXPRESSINDO' => 'DPE',
    'PT. ZONA LINTAS SAMUDERA' => 'ZONA',
];

$totalUpdated = 0;

try {
    DB::beginTransaction();

    echo "Starting vendor name updates...\n\n";

    // Update kontainers table
    if (Schema::hasTable('kontainers')) {
        echo "Processing table: kontainers\n";
        echo "-----------------------------------\n";
        
        foreach ($vendorMapping as $oldName => $newName) {
            $count = DB::table('kontainers')
                ->where('vendor', $oldName)
                ->count();
            
            if ($count > 0) {
                DB::table('kontainers')
                    ->where('vendor', $oldName)
                    ->update(['vendor' => $newName]);
                
                echo "✓ Updated '{$oldName}' to '{$newName}': {$count} records\n";
                $totalUpdated += $count;
            } else {
                echo "○ No records found for '{$oldName}'\n";
            }
        }
        echo "\n";
    }

    // Update tagihan_kontainer_sewa table
    if (Schema::hasTable('tagihan_kontainer_sewa')) {
        echo "Processing table: tagihan_kontainer_sewa\n";
        echo "-----------------------------------\n";
        
        foreach ($vendorMapping as $oldName => $newName) {
            $count = DB::table('tagihan_kontainer_sewa')
                ->where('vendor', $oldName)
                ->count();
            
            if ($count > 0) {
                DB::table('tagihan_kontainer_sewa')
                    ->where('vendor', $oldName)
                    ->update(['vendor' => $newName]);
                
                echo "✓ Updated '{$oldName}' to '{$newName}': {$count} records\n";
                $totalUpdated += $count;
            } else {
                echo "○ No records found for '{$oldName}'\n";
            }
        }
        echo "\n";
    }

    // Update pranota_tagihan_kontainers table if exists
    if (Schema::hasTable('pranota_tagihan_kontainers')) {
        echo "Processing table: pranota_tagihan_kontainers\n";
        echo "-----------------------------------\n";
        
        foreach ($vendorMapping as $oldName => $newName) {
            $count = DB::table('pranota_tagihan_kontainers')
                ->where('vendor', $oldName)
                ->count();
            
            if ($count > 0) {
                DB::table('pranota_tagihan_kontainers')
                    ->where('vendor', $oldName)
                    ->update(['vendor' => $newName]);
                
                echo "✓ Updated '{$oldName}' to '{$newName}': {$count} records\n";
                $totalUpdated += $count;
            } else {
                echo "○ No records found for '{$oldName}'\n";
            }
        }
        echo "\n";
    }

    DB::commit();

    echo "=================================================\n";
    echo "✓ SUCCESS! Total records updated: {$totalUpdated}\n";
    echo "=================================================\n\n";

    // Display final vendor summary
    echo "Final Vendor Summary in kontainers table:\n";
    echo "-----------------------------------\n";
    
    $vendors = DB::table('kontainers')
        ->select('vendor', DB::raw('count(*) as total'))
        ->whereNotNull('vendor')
        ->groupBy('vendor')
        ->orderBy('vendor')
        ->get();
    
    foreach ($vendors as $vendor) {
        echo "  {$vendor->vendor}: {$vendor->total} records\n";
    }
    echo "\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n";
    echo "=================================================\n";
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "=================================================\n";
    echo "\nTransaction rolled back. No changes were made.\n\n";
    exit(1);
}

echo "Script completed successfully!\n\n";
