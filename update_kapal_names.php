<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting update of kapal names...\n";

// Target: Change specific "KM SUMBER ABADI X" to "KM SUMBER ABADI 178"
$targetName = 'KM SUMBER ABADI 178';

// List of names to update based on request "179, 180, dll"
// We include variations with "KM." and "KM"
$namesToUpdate = [
    'KM. SUMBER ABADI 178', // Normalize dot
    'KM SUMBER ABADI 179',
    'KM. SUMBER ABADI 179',
    'KM SUMBER ABADI 180',
    'KM. SUMBER ABADI 180',
    // Add more here if needed, e.g. 181, 182... based on "DLL" if intended
];

// Verify count before update
$count = DB::table('bls')->whereIn('nama_kapal', $namesToUpdate)->count();
echo "Found {$count} records to update.\n";

if ($count > 0) {
    DB::table('bls')
        ->whereIn('nama_kapal', $namesToUpdate)
        ->update(['nama_kapal' => $targetName]);
    
    echo "Successfully updated records to '{$targetName}'.\n";
} else {
    echo "No records found matching the criteria.\n";
}

echo "Done.\n";
