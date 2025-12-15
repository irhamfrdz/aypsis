<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;
use App\Models\NaikKapal;

echo "=== Checking for Sentosa ship data ===\n\n";

// Check BL table with LIKE
echo "1. BL table with LIKE '%Sentosa%':\n";
$blLike = Bl::where('nama_kapal', 'like', '%Sentosa%')
    ->where('no_voyage', 'ST05PJ25')
    ->get(['id', 'nama_kapal', 'no_voyage', 'nomor_kontainer']);
echo "   Found: " . $blLike->count() . " records\n";
foreach ($blLike as $bl) {
    echo "   - ID: {$bl->id}, Kapal: '{$bl->nama_kapal}', Voyage: '{$bl->no_voyage}', Kontainer: '{$bl->nomor_kontainer}'\n";
}
echo "\n";

// Check BL table exact match
echo "2. BL table with exact 'KM Sentosa 18':\n";
$blExact = Bl::where('nama_kapal', 'KM Sentosa 18')
    ->where('no_voyage', 'ST05PJ25')
    ->get(['id', 'nama_kapal', 'no_voyage', 'nomor_kontainer']);
echo "   Found: " . $blExact->count() . " records\n";
foreach ($blExact as $bl) {
    echo "   - ID: {$bl->id}, Kapal: '{$bl->nama_kapal}', Voyage: '{$bl->no_voyage}', Kontainer: '{$bl->nomor_kontainer}'\n";
}
echo "\n";

// Check NaikKapal table with LIKE
echo "3. NaikKapal table with LIKE '%Sentosa%':\n";
$nkLike = NaikKapal::where('nama_kapal', 'like', '%Sentosa%')
    ->where('no_voyage', 'ST05PJ25')
    ->get(['id', 'nama_kapal', 'no_voyage', 'nomor_kontainer']);
echo "   Found: " . $nkLike->count() . " records\n";
foreach ($nkLike as $nk) {
    echo "   - ID: {$nk->id}, Kapal: '{$nk->nama_kapal}', Voyage: '{$nk->no_voyage}', Kontainer: '{$nk->nomor_kontainer}'\n";
}
echo "\n";

// Check NaikKapal table exact match
echo "4. NaikKapal table with exact 'KM Sentosa 18':\n";
$nkExact = NaikKapal::where('nama_kapal', 'KM Sentosa 18')
    ->where('no_voyage', 'ST05PJ25')
    ->get(['id', 'nama_kapal', 'no_voyage', 'nomor_kontainer']);
echo "   Found: " . $nkExact->count() . " records\n";
foreach ($nkExact as $nk) {
    echo "   - ID: {$nk->id}, Kapal: '{$nk->nama_kapal}', Voyage: '{$nk->no_voyage}', Kontainer: '{$nk->nomor_kontainer}'\n";
}
echo "\n";

// Check unique ship names containing Sentosa
echo "5. All unique ship names containing 'Sentosa' in BL:\n";
$ships = Bl::where('nama_kapal', 'like', '%Sentosa%')
    ->distinct()
    ->pluck('nama_kapal');
foreach ($ships as $ship) {
    echo "   - '{$ship}'\n";
}
echo "\n";

echo "6. All unique ship names containing 'Sentosa' in NaikKapal:\n";
$ships = NaikKapal::where('nama_kapal', 'like', '%Sentosa%')
    ->distinct()
    ->pluck('nama_kapal');
foreach ($ships as $ship) {
    echo "   - '{$ship}'\n";
}
echo "\n";

// Check voyages for this ship
echo "7. All voyages for 'KM Sentosa 18' in BL:\n";
$voyages = Bl::where('nama_kapal', 'KM Sentosa 18')
    ->distinct()
    ->pluck('no_voyage');
foreach ($voyages as $voyage) {
    echo "   - '{$voyage}'\n";
}
echo "\n";

echo "8. All voyages for 'KM Sentosa 18' in NaikKapal:\n";
$voyages = NaikKapal::where('nama_kapal', 'KM Sentosa 18')
    ->distinct()
    ->pluck('no_voyage');
foreach ($voyages as $voyage) {
    echo "   - '{$voyage}'\n";
}

echo "\n=== Check complete ===\n";
