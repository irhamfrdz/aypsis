<?php

/**
 * Script untuk mengecek data no_seal di BL dan Prospek
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=================================================\n";
echo "Checking NO SEAL Data in BL and Prospek Tables\n";
echo "=================================================\n\n";

// Get first 10 BLs with their prospek data
$bls = DB::table('bls')
    ->select('bls.id', 'bls.nomor_kontainer', 'bls.no_seal as bl_no_seal', 'bls.prospek_id', 'prospek.no_seal as prospek_no_seal')
    ->leftJoin('prospek', 'bls.prospek_id', '=', 'prospek.id')
    ->limit(10)
    ->get();

echo "Sample Data (First 10 Records):\n";
echo str_repeat("-", 120) . "\n";
printf("%-5s | %-15s | %-15s | %-12s | %-15s\n", "ID", "Kontainer", "BL No Seal", "Prospek ID", "Prospek No Seal");
echo str_repeat("-", 120) . "\n";

foreach ($bls as $bl) {
    printf("%-5s | %-15s | %-15s | %-12s | %-15s\n", 
        $bl->id,
        $bl->nomor_kontainer ?: '-',
        $bl->bl_no_seal ?: '-',
        $bl->prospek_id ?: 'NULL',
        $bl->prospek_no_seal ?: '-'
    );
}

echo "\n=================================================\n";
echo "Summary:\n";
echo "=================================================\n";

// Count BLs without no_seal
$blsWithoutSeal = DB::table('bls')
    ->whereNull('no_seal')
    ->orWhere('no_seal', '')
    ->count();

echo "BLs without no_seal: {$blsWithoutSeal}\n";

// Count BLs with prospek_id
$blsWithProspek = DB::table('bls')
    ->whereNotNull('prospek_id')
    ->count();

echo "BLs with prospek_id: {$blsWithProspek}\n";

// Count BLs without no_seal but have prospek_id
$blsNeedUpdate = DB::table('bls')
    ->whereNotNull('prospek_id')
    ->where(function($q) {
        $q->whereNull('no_seal')->orWhere('no_seal', '');
    })
    ->count();

echo "BLs without no_seal BUT have prospek_id: {$blsNeedUpdate}\n";

// Count prospeks with no_seal
$prospeksWithSeal = DB::table('prospek')
    ->whereNotNull('no_seal')
    ->where('no_seal', '!=', '')
    ->count();

echo "Prospeks with no_seal data: {$prospeksWithSeal}\n";

echo "=================================================\n";
