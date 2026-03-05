<?php

/**
 * Script to fix Cargo container numbers
 * Ensures that all 'Cargo' type records have 'CARGO' as the container number.
 * 
 * Usage: php fix_cargo_container_number.php
 */

use Illuminate\Support\Facades\DB;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\Prospek;

// 1. Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting data fix for Cargo container numbers...\n";
echo "-----------------------------------------------\n";

try {
    DB::beginTransaction();

    // --- Part 1: Fix TandaTerimaTanpaSuratJalan ---
    echo "Checking TandaTerimaTanpaSuratJalan records...\n";
    
    $tttsjCount = TandaTerimaTanpaSuratJalan::where('tipe_kontainer', 'cargo')
        ->where(function($query) {
            $query->whereNull('no_kontainer')
                  ->orWhere('no_kontainer', '')
                  ->orWhere('no_kontainer', '!=', 'CARGO');
        })
        ->count();

    echo "Found $tttsjCount records in TandaTerimaTanpaSuratJalan to update.\n";

    if ($tttsjCount > 0) {
        $updatedTttsj = TandaTerimaTanpaSuratJalan::where('tipe_kontainer', 'cargo')
            ->where(function($query) {
                $query->whereNull('no_kontainer')
                      ->orWhere('no_kontainer', '')
                      ->orWhere('no_kontainer', '!=', 'CARGO');
            })
            ->update(['no_kontainer' => 'CARGO']);
            
        echo "Successfully updated $updatedTttsj records in TandaTerimaTanpaSuratJalan table.\n";
    }

    // --- Part 2: Fix Prospek ---
    echo "\nChecking Prospek records...\n";
    
    // In Prospek, tipe is usually uppercase 'CARGO'
    $prospekCount = Prospek::where('tipe', 'CARGO')
        ->where(function($query) {
            $query->whereNull('nomor_kontainer')
                  ->orWhere('nomor_kontainer', '')
                  ->orWhere('nomor_kontainer', '!=', 'CARGO');
        })
        ->count();

    echo "Found $prospekCount records in Prospek to update.\n";

    if ($prospekCount > 0) {
        $updatedProspek = Prospek::where('tipe', 'CARGO')
            ->where(function($query) {
                $query->whereNull('nomor_kontainer')
                      ->orWhere('nomor_kontainer', '')
                      ->orWhere('nomor_kontainer', '!=', 'CARGO');
            })
            ->update(['nomor_kontainer' => 'CARGO']);
            
        echo "Successfully updated $updatedProspek records in Prospek table.\n";
    }

    DB::commit();
    echo "\n-----------------------------------------------\n";
    echo "Data fix completed successfully!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\nERROR: An error occurred during the update:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
