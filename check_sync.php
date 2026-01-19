<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;

$voyage = 'SA01JP26';

// Get all naik_kapal sudah_ob
$naikKapals = NaikKapal::where('no_voyage', $voyage)
    ->where('sudah_ob', true)
    ->get();

// Get existing manifest containers
$manifestContainers = Manifest::where('no_voyage', $voyage)
    ->pluck('nomor_kontainer')
    ->toArray();

echo "=== Missing in Manifest ===" . PHP_EOL;
$missing = [];
foreach ($naikKapals as $nk) {
    if (!in_array($nk->nomor_kontainer, $manifestContainers)) {
        $missing[] = [
            'id' => $nk->id,
            'nomor_kontainer' => $nk->nomor_kontainer,
            'jenis_barang' => $nk->jenis_barang,
        ];
        echo "ID: {$nk->id} | Container: {$nk->nomor_kontainer} | Barang: " . substr($nk->jenis_barang ?? '-', 0, 30) . PHP_EOL;
    }
}

echo PHP_EOL . "Total missing: " . count($missing) . PHP_EOL;

// Check for duplicates in naik_kapal
echo PHP_EOL . "=== Duplicate Containers in NaikKapal (sudah_ob) ===" . PHP_EOL;
$containers = $naikKapals->pluck('nomor_kontainer')->toArray();
$counts = array_count_values($containers);
$duplicates = array_filter($counts, function($count) {
    return $count > 1;
});

if (count($duplicates) > 0) {
    foreach ($duplicates as $container => $count) {
        echo "Container: {$container} appears {$count} times" . PHP_EOL;
    }
} else {
    echo "No duplicates found" . PHP_EOL;
}
