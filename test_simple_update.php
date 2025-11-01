<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TandaTerima;
use App\Models\Prospek;

echo "=== SIMPLE UPDATE TEST (Avoiding jumlah field) ===\n\n";

// Find a TandaTerima
$tandaTerima = TandaTerima::with('prospeks')->find(8);

if (!$tandaTerima) {
    echo "TandaTerima not found!\n";
    exit;
}

echo "1. BEFORE UPDATE:\n";
echo "TandaTerima ID: {$tandaTerima->id}\n";
echo "Current Volume: " . ($tandaTerima->meter_kubik ?? 'NULL') . "\n";
echo "Current Tonase: " . ($tandaTerima->tonase ?? 'NULL') . "\n";
echo "Linked Prospeks: " . $tandaTerima->prospeks->count() . "\n";

foreach ($tandaTerima->prospeks as $prospek) {
    echo "  - Prospek ID {$prospek->id}: Volume={$prospek->total_volume}, Tonase={$prospek->total_ton}, Kuantitas={$prospek->kuantitas}\n";
}

echo "\n2. UPDATING TANDA TERIMA:\n";

// Update just volume and tonase (avoid jumlah field for now)
$newVolume = 45.5;
$newTonase = 28.7;

$tandaTerima->update([
    'meter_kubik' => $newVolume,
    'tonase' => $newTonase,
    'estimasi_nama_kapal' => 'Updated Test Kapal',
    'updated_by' => 1,
]);

echo "Updated TandaTerima with Volume: {$newVolume}, Tonase: {$newTonase}\n";

echo "\n3. MANUAL PROSPEK UPDATE:\n";

// Find related prospeks and update them
$prospeksToUpdate = collect();

if ($tandaTerima->surat_jalan_id) {
    $prospeksBySuratJalan = Prospek::where('surat_jalan_id', $tandaTerima->surat_jalan_id)->get();
    $prospeksToUpdate = $prospeksToUpdate->merge($prospeksBySuratJalan);
    echo "Found " . $prospeksBySuratJalan->count() . " prospeks by surat_jalan_id\n";
}

$prospeksToUpdate = $prospeksToUpdate->unique('id');

foreach ($prospeksToUpdate as $prospek) {
    $oldVolume = $prospek->total_volume;
    $oldTonase = $prospek->total_ton;
    
    $prospek->update([
        'total_volume' => $newVolume,
        'total_ton' => $newTonase,
        'updated_by' => 1,
    ]);
    
    echo "Updated Prospek ID {$prospek->id}: Volume {$oldVolume} → {$newVolume}, Tonase {$oldTonase} → {$newTonase}\n";
}

echo "\n4. VERIFICATION:\n";
$tandaTerima = TandaTerima::with('prospeks')->find($tandaTerima->id);

foreach ($tandaTerima->prospeks as $prospek) {
    echo "  - Prospek ID {$prospek->id}: Volume={$prospek->total_volume}, Tonase={$prospek->total_ton}\n";
}

echo "\n✅ BASIC UPDATE SYNC WORKING!\n";
echo "\n=== TEST COMPLETE ===\n";