<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Updating NULL size values with estimated sizes...\n";

// Get records with NULL size
$nullSizeRecords = DaftarTagihanKontainerSewa::whereNull('size')->get();
echo "Found {$nullSizeRecords->count()} records with NULL size\n";

$fixed = 0;
$notFixed = 0;

foreach ($nullSizeRecords as $record) {
    $nomor = $record->nomor_kontainer;
    $vendor = $record->vendor;

    // Try to estimate size based on common patterns
    $estimatedSize = null;

    // Common container number patterns that might indicate size
    if (preg_match('/20/', $nomor)) {
        $estimatedSize = '20';
    } elseif (preg_match('/40/', $nomor)) {
        $estimatedSize = '40';
    } else {
        // Default based on vendor or just set to most common size
        if (strtoupper($vendor) === 'DPE') {
            $estimatedSize = '20'; // DPE commonly uses 20ft
        } elseif (strtoupper($vendor) === 'ZONA') {
            $estimatedSize = '40'; // ZONA might commonly use 40ft
        } else {
            $estimatedSize = '20'; // Default to 20ft as it's most common
        }
    }

    if ($estimatedSize) {
        $record->size = $estimatedSize;
        $record->save();
        echo "Updated record ID {$record->id}: {$nomor} ({$vendor}) -> size {$estimatedSize}\n";
        $fixed++;
    } else {
        echo "Could not estimate size for record ID {$record->id}: {$nomor}\n";
        $notFixed++;
    }
}

echo "\nSummary:\n";
echo "Fixed: {$fixed} records\n";
echo "Not fixed: {$notFixed} records\n";

// Show updated count
$remainingNull = DaftarTagihanKontainerSewa::whereNull('size')->count();
echo "Remaining records with NULL size: {$remainingNull}\n";

// Show some statistics
echo "\nSize distribution after update:\n";
$sizeStats = DaftarTagihanKontainerSewa::selectRaw('size, COUNT(*) as count')
    ->groupBy('size')
    ->orderBy('size')
    ->get();

foreach ($sizeStats as $stat) {
    $size = $stat->size ?? 'NULL';
    echo "Size {$size}: {$stat->count} records\n";
}
