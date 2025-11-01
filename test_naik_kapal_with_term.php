<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use App\Models\Bl;
use App\Models\TandaTerima;

echo "=== TESTING NAIK KAPAL PROCESS WITH TERM DATA ===\n\n";

// Check the prospek that has term data
$prospekWithTerm = Prospek::with('tandaTerima')->where('id', 37)->first();

if (!$prospekWithTerm) {
    echo "Prospek ID 37 not found!\n";
    exit;
}

echo "1. PROSPEK DATA BEFORE NAIK KAPAL:\n";
echo "Prospek ID: {$prospekWithTerm->id}\n";
echo "Tanda Terima ID: {$prospekWithTerm->tanda_terima_id}\n";
echo "Term from TandaTerima: " . ($prospekWithTerm->tandaTerima ? $prospekWithTerm->tandaTerima->term : 'NULL') . "\n";
echo "Prospek Status: {$prospekWithTerm->status}\n";
echo "Kapal: {$prospekWithTerm->kapal}\n";
echo "Volume: {$prospekWithTerm->volume}\n\n";

// Check existing BL records for this prospek
$existingBls = Bl::where('prospek_id', $prospekWithTerm->id)->get();
echo "2. EXISTING BL RECORDS:\n";
if ($existingBls->count() > 0) {
    foreach ($existingBls as $bl) {
        echo "BL ID: {$bl->id}, No BL: {$bl->no_bl}, Volume: {$bl->volume}, Term: " . ($bl->term ?? 'NULL') . "\n";
    }
} else {
    echo "No existing BL records found for this prospek.\n";
}

echo "\n3. SIMULATING NAIK KAPAL PROCESS:\n";

// Simulate the naik kapal data that would be submitted
$naikKapalData = [
    'prospek_id' => $prospekWithTerm->id,
    'total_volume' => 100, // Sample volume
    'kapal' => 'Test Kapal 123',
    'no_voyage' => 'VOY123',
    'tanggal_berangkat' => date('Y-m-d'),
];

echo "Naik Kapal Data:\n";
foreach ($naikKapalData as $key => $value) {
    echo "- {$key}: {$value}\n";
}

// Manual BL creation to test the logic (simulating what executeNaikKapal would do)
echo "\n4. CREATING TEST BL:\n";

$blData = [
    'prospek_id' => $prospekWithTerm->id,
    'no_bl' => 'TEST-BL-' . time(),
    'volume' => $naikKapalData['total_volume'],
    'term' => $prospekWithTerm->tandaTerima ? $prospekWithTerm->tandaTerima->term : null,
    'kapal' => $naikKapalData['kapal'],
    'no_voyage' => $naikKapalData['no_voyage'],
    'tanggal_berangkat' => $naikKapalData['tanggal_berangkat'],
    'created_at' => now(),
    'updated_at' => now(),
];

try {
    $newBl = Bl::create($blData);
    echo "Successfully created BL ID: {$newBl->id}\n";
    echo "BL Details:\n";
    echo "- No BL: {$newBl->no_bl}\n";
    echo "- Volume: {$newBl->volume}\n";
    echo "- Term: " . ($newBl->term ?? 'NULL') . "\n";
    echo "- Kapal: {$newBl->kapal}\n";
    echo "- No Voyage: {$newBl->no_voyage}\n";
    
} catch (Exception $e) {
    echo "Error creating BL: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";