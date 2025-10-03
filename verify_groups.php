<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Verification of Group Updates\n";
echo "==============================\n\n";

// Check specific containers mentioned in CSV
$testContainers = [
    'CCLU3836629',
    'CBHU3952697',
    'CBHU5911444',
    'RXTU4540180'
];

foreach ($testContainers as $kontainer) {
    echo "Container: {$kontainer}\n";

    $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)
        ->orderBy('periode')
        ->get();

    if ($records->isEmpty()) {
        echo "  (not found in database)\n\n";
        continue;
    }

    foreach ($records as $record) {
        echo "  Periode {$record->periode}: Group '{$record->group}' - {$record->vendor} - ";
        echo "Tarif: {$record->tarif} - ";
        echo "DPP: " . number_format($record->dpp, 0) . "\n";
    }
    echo "\n";
}

// Show group distribution
echo "Group Distribution:\n";
echo "===================\n";
$groupCounts = DaftarTagihanKontainerSewa::selectRaw('`group`, COUNT(*) as count')
    ->whereNotNull('group')
    ->where('group', '!=', '')
    ->groupBy('group')
    ->orderBy('group')
    ->get();

foreach ($groupCounts as $row) {
    echo "Group {$row->group}: {$row->count} containers\n";
}

echo "\nTotal unique groups: " . $groupCounts->count() . "\n";
