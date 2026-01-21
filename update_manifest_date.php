<?php

use Illuminate\Support\Facades\DB;
use App\Models\Manifest;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$voyage = 'SP01BJ26';
$newDate = '2026-01-19';

echo "Updating tanggal_berangkat for voyage '$voyage' to '$newDate'...\n";

try {
    $count = Manifest::where('no_voyage', $voyage)
        ->update(['tanggal_berangkat' => $newDate]);

    echo "Success! Updated $count records in 'manifests' table.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
