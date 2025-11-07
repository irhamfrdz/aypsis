<?php

require_once 'bootstrap/app.php';

use App\Models\MasterKapal;
use App\Models\PergerakanKapal;

echo "=== DEBUG OB MUAT DATA ===\n\n";

// Master Kapal
echo "1. MASTER KAPAL:\n";
$masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
echo "Total: " . $masterKapals->count() . "\n";
$masterKapals->take(10)->each(function($kapal) {
    echo "- {$kapal->nama_kapal} (Status: {$kapal->status})\n";
});

echo "\n2. PERGERAKAN KAPAL:\n";
$pergerakanKapals = PergerakanKapal::whereNotNull('voyage')
                                  ->where('voyage', '!=', '')
                                  ->orderBy('nama_kapal')
                                  ->orderBy('voyage')
                                  ->get();
echo "Total: " . $pergerakanKapals->count() . "\n";
$pergerakanKapals->take(10)->each(function($pergerakan) {
    echo "- {$pergerakan->nama_kapal} | Voyage: {$pergerakan->voyage} | Status: {$pergerakan->status}\n";
});

echo "\n3. GROUPING BY KAPAL:\n";
$groupedVoyages = $pergerakanKapals->groupBy('nama_kapal');
foreach($groupedVoyages->take(5) as $namaKapal => $voyages) {
    echo "Kapal: $namaKapal\n";
    foreach($voyages as $voyage) {
        echo "  - Voyage: {$voyage->voyage} ({$voyage->status})\n";
    }
    echo "\n";
}

echo "\n4. NAME MATCHING:\n";
$masterNames = $masterKapals->pluck('nama_kapal')->toArray();
$pergerakanNames = $pergerakanKapals->pluck('nama_kapal')->unique()->toArray();
$commonNames = array_intersect($masterNames, $pergerakanNames);

echo "Common names: " . count($commonNames) . "\n";
foreach($commonNames as $name) {
    echo "- $name\n";
}

echo "\nMaster only: " . count(array_diff($masterNames, $pergerakanNames)) . "\n";
echo "Pergerakan only: " . count(array_diff($pergerakanNames, $masterNames)) . "\n";

echo "\n=== END DEBUG ===\n";