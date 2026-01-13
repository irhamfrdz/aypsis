<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$voyage = 'SR01JB26';

echo "=== REMOVE DUPLICATE BLS FOR VOYAGE {$voyage} (EXCEPT CARGO) ===\n\n";

// Find all duplicates
$duplicates = DB::table('bls')
    ->select('nomor_kontainer', DB::raw('COUNT(*) as cnt'))
    ->where('no_voyage', $voyage)
    ->groupBy('nomor_kontainer')
    ->havingRaw('COUNT(*) > 1')
    ->get();

$totalDeleted = 0;
$skippedCargo = 0;

foreach ($duplicates as $dup) {
    $kontainer = $dup->nomor_kontainer;
    $count = $dup->cnt;
    
    // Skip CARGO
    if (strtoupper(trim($kontainer)) === 'CARGO') {
        echo "⏭️  Skipping CARGO ({$count} records) - keeping all CARGO records\n";
        $skippedCargo += $count;
        continue;
    }
    
    echo "🔍 Processing {$kontainer} ({$count} duplicates)...\n";
    
    // Get all records for this container, ordered by ID
    $records = DB::table('bls')
        ->where('no_voyage', $voyage)
        ->where('nomor_kontainer', $kontainer)
        ->orderBy('id', 'asc')
        ->get();
    
    if ($records->count() <= 1) {
        continue;
    }
    
    // Keep the first record (lowest ID), delete the rest
    $keepId = $records->first()->id;
    $deleteIds = [];
    
    foreach ($records as $index => $record) {
        if ($index === 0) {
            echo "   ✅ Keeping BLS ID {$record->id} (nomor_bl: {$record->nomor_bl})\n";
        } else {
            echo "   ❌ Deleting BLS ID {$record->id} (nomor_bl: {$record->nomor_bl})\n";
            $deleteIds[] = $record->id;
        }
    }
    
    // Delete duplicates
    if (count($deleteIds) > 0) {
        DB::table('bls')->whereIn('id', $deleteIds)->delete();
        $totalDeleted += count($deleteIds);
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total deleted: {$totalDeleted}\n";
echo "CARGO records kept: {$skippedCargo}\n";

// Verify final count
$finalCount = DB::table('bls')->where('no_voyage', $voyage)->count();
$uniqueCount = DB::table('bls')->where('no_voyage', $voyage)->distinct()->count('nomor_kontainer');

echo "\nFinal BLS count for {$voyage}: {$finalCount}\n";
echo "Unique containers: {$uniqueCount}\n";
