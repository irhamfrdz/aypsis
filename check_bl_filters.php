<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;
use Illuminate\Support\Facades\DB;

echo "=== Checking BL filters for KM Sentosa 18 / ST05PJ25 ===\n\n";

// Get all records for this ship/voyage
$allRecords = Bl::where('nama_kapal', 'KM Sentosa 18')
    ->where('no_voyage', 'ST05PJ25')
    ->get();

echo "1. Total records: " . $allRecords->count() . "\n\n";

// Check tipe_kontainer distribution
echo "2. Tipe Kontainer distribution:\n";
$tipeGroups = $allRecords->groupBy('tipe_kontainer');
foreach ($tipeGroups as $tipe => $records) {
    echo "   - " . ($tipe ?: '(NULL)') . ": " . $records->count() . " records\n";
}
echo "\n";

// Check CARGO exclusion impact
echo "3. CARGO exclusion check:\n";
$cargoExcluded = $allRecords->filter(function($bl) {
    return $bl->tipe_kontainer === 'CARGO' || 
           ($bl->tipe_kontainer === 'FCL' && 
            ($bl->nomor_kontainer === 'CARGO' || strpos($bl->nomor_kontainer, 'CARGO') === 0));
});
echo "   - Records excluded by CARGO filter: " . $cargoExcluded->count() . "\n";
echo "   - Records remaining after CARGO filter: " . ($allRecords->count() - $cargoExcluded->count()) . "\n\n";

// Sample of excluded records
if ($cargoExcluded->count() > 0) {
    echo "   Sample excluded records:\n";
    foreach ($cargoExcluded->take(5) as $bl) {
        echo "   - ID: {$bl->id}, Tipe: '{$bl->tipe_kontainer}', Kontainer: '{$bl->nomor_kontainer}'\n";
    }
    echo "\n";
}

// Apply the exact same filter as controller
echo "4. Testing controller query:\n";
$queryResult = Bl::where('nama_kapal', 'KM Sentosa 18')
    ->where('no_voyage', 'ST05PJ25')
    ->whereRaw("NOT (tipe_kontainer = 'CARGO' OR (tipe_kontainer = 'FCL' AND (nomor_kontainer = 'CARGO' OR nomor_kontainer LIKE 'CARGO%')))")
    ->get();

echo "   - Records after controller filter: " . $queryResult->count() . "\n\n";

// Sample of remaining records
if ($queryResult->count() > 0) {
    echo "   Sample remaining records:\n";
    foreach ($queryResult->take(5) as $bl) {
        echo "   - ID: {$bl->id}, Tipe: '{$bl->tipe_kontainer}', Kontainer: '{$bl->nomor_kontainer}', Size: '{$bl->size_kontainer}'\n";
    }
} else {
    echo "   NO RECORDS REMAINING!\n";
    echo "   All 86 records are being filtered out.\n\n";
    echo "   Breakdown of why:\n";
    
    $cargo = Bl::where('nama_kapal', 'KM Sentosa 18')
        ->where('no_voyage', 'ST05PJ25')
        ->where('tipe_kontainer', 'CARGO')
        ->count();
    echo "   - tipe_kontainer = 'CARGO': {$cargo} records\n";
    
    $fclCargo = Bl::where('nama_kapal', 'KM Sentosa 18')
        ->where('no_voyage', 'ST05PJ25')
        ->where('tipe_kontainer', 'FCL')
        ->where(function($q) {
            $q->where('nomor_kontainer', 'CARGO')
              ->orWhere('nomor_kontainer', 'like', 'CARGO%');
        })
        ->count();
    echo "   - tipe_kontainer = 'FCL' AND nomor_kontainer starts with CARGO: {$fclCargo} records\n";
}

echo "\n=== Check complete ===\n";
