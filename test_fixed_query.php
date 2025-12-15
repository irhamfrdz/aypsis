<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;

echo "=== Testing FIXED query ===\n\n";

$count = Bl::where('nama_kapal', 'KM Sentosa 18')
    ->where('no_voyage', 'ST05PJ25')
    ->whereRaw("NOT (COALESCE(tipe_kontainer, '') = 'CARGO' OR (COALESCE(tipe_kontainer, '') = 'FCL' AND (COALESCE(nomor_kontainer, '') = 'CARGO' OR COALESCE(nomor_kontainer, '') LIKE 'CARGO%')))")
    ->count();

echo "Records with FIXED query: {$count}\n";

if ($count > 0) {
    echo "\nSUCCESS! Fix is working correctly.\n";
} else {
    echo "\nFAILED! Still returning 0 records.\n";
}

echo "\n=== Test complete ===\n";
