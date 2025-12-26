<?php

/**
 * Script untuk update size_kontainer yang kosong
 * Jalankan: php update_size_kontainer.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking naik_kapal records with empty size_kontainer...\n\n";

// Count records with null or empty size_kontainer
$emptyCount = DB::table('naik_kapal')
    ->whereNull('size_kontainer')
    ->orWhere('size_kontainer', '')
    ->count();

echo "Found {$emptyCount} records with empty size_kontainer\n\n";

if ($emptyCount === 0) {
    echo "All records have size_kontainer filled!\n";
    exit(0);
}

// Show some examples
echo "Sample records with empty size_kontainer:\n";
$samples = DB::table('naik_kapal')
    ->select('id', 'nomor_kontainer', 'tipe_kontainer', 'size_kontainer', 'ukuran_kontainer')
    ->whereNull('size_kontainer')
    ->orWhere('size_kontainer', '')
    ->limit(10)
    ->get();

foreach ($samples as $sample) {
    echo sprintf(
        "ID: %d | Container: %s | Tipe: %s | Size: %s | Ukuran: %s\n",
        $sample->id,
        $sample->nomor_kontainer,
        $sample->tipe_kontainer ?? 'null',
        $sample->size_kontainer ?? 'null',
        $sample->ukuran_kontainer ?? 'null'
    );
}

echo "\n";
echo "Do you want to:\n";
echo "1. Copy ukuran_kontainer to size_kontainer (if ukuran_kontainer has data)\n";
echo "2. Set default size based on tipe_kontainer (FCL=40, LCL=20)\n";
echo "3. Exit without changes\n";
echo "\nEnter choice (1/2/3): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$choice = trim($line);
fclose($handle);

switch ($choice) {
    case '1':
        // Copy ukuran_kontainer to size_kontainer
        echo "\nCopying ukuran_kontainer to size_kontainer...\n";
        $updated = DB::table('naik_kapal')
            ->whereNotNull('ukuran_kontainer')
            ->where('ukuran_kontainer', '!=', '')
            ->where(function($query) {
                $query->whereNull('size_kontainer')
                      ->orWhere('size_kontainer', '');
            })
            ->update(['size_kontainer' => DB::raw('ukuran_kontainer')]);
        
        echo "Updated {$updated} records from ukuran_kontainer\n";
        break;
        
    case '2':
        // Set default based on tipe
        echo "\nSetting default size based on tipe_kontainer...\n";
        
        // FCL = 40
        $updatedFCL = DB::table('naik_kapal')
            ->where('tipe_kontainer', 'FCL')
            ->where(function($query) {
                $query->whereNull('size_kontainer')
                      ->orWhere('size_kontainer', '');
            })
            ->update(['size_kontainer' => '40']);
        
        // LCL = 20
        $updatedLCL = DB::table('naik_kapal')
            ->where('tipe_kontainer', 'LCL')
            ->where(function($query) {
                $query->whereNull('size_kontainer')
                      ->orWhere('size_kontainer', '');
            })
            ->update(['size_kontainer' => '20']);
        
        echo "Updated {$updatedFCL} FCL records to size 40\n";
        echo "Updated {$updatedLCL} LCL records to size 20\n";
        break;
        
    case '3':
        echo "\nExiting without changes.\n";
        break;
        
    default:
        echo "\nInvalid choice. Exiting.\n";
        break;
}

// Check remaining empty
$remainingEmpty = DB::table('naik_kapal')
    ->whereNull('size_kontainer')
    ->orWhere('size_kontainer', '')
    ->count();

echo "\nRemaining records with empty size_kontainer: {$remainingEmpty}\n";
echo "Done!\n";
