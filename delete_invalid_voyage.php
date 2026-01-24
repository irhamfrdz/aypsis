<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$voyage = 'SA01PJ26'; // Correction: The requested value in the image is SA01PJ26, but the user text says SA01JP26 (JP vs PJ). The image clearly shows 'SA01PJ26'. I will use the one matching the text prompt, but I should double check. Wait, user prompt says "SA01PJ26" (PJ), matching the image. The previous voyages seen were SA01JP26 (JP). 
// Let's assume the user wants to delete the one ending in PJ26 as written.

echo "Deleting data for Voyage '$voyage'...\n";

// 1. Delete from naik_kapal
$count1 = DB::table('naik_kapal')->where('no_voyage', $voyage)->count();
if ($count1 > 0) {
    DB::table('naik_kapal')->where('no_voyage', $voyage)->delete();
    echo "- Deleted $count1 records from 'naik_kapal'.\n";
} else {
    echo "- No records found in 'naik_kapal'.\n";
}

// 2. Delete from bls
$count2 = DB::table('bls')->where('no_voyage', $voyage)->count();
if ($count2 > 0) {
    DB::table('bls')->where('no_voyage', $voyage)->delete();
    echo "- Deleted $count2 records from 'bls'.\n";
} else {
    echo "- No records found in 'bls'.\n";
}

// 3. Delete from manifests (if that table exists and uses no_voyage)
// Checking if 'manifests' table exists first to avoid error
$hasManifests = false;
try {
    $hasManifests = DB::getSchemaBuilder()->hasTable('manifests');
} catch (\Exception $e) {}

if ($hasManifests) {
    $count3 = DB::table('manifests')->where('no_voyage', $voyage)->count();
    if ($count3 > 0) {
        DB::table('manifests')->where('no_voyage', $voyage)->delete();
        echo "- Deleted $count3 records from 'manifests'.\n";
    } else {
        echo "- No records found in 'manifests'.\n";
    }
}

echo "Done.\n";
