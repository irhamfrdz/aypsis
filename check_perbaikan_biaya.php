<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Perbaikan Kontainer Biaya Values ===\n\n";

try {
    $perbaikans = DB::table('perbaikan_kontainers')->select('id', 'estimasi_biaya_perbaikan', 'realisasi_biaya_perbaikan')->get();

    echo "Perbaikan Kontainer Biaya Values:\n";
    echo str_repeat("=", 60) . "\n";

    foreach ($perbaikans as $perbaikan) {
        echo sprintf("ID: %d | Estimasi: %s | Realisasi: %s\n",
            $perbaikan->id,
            $perbaikan->estimasi_biaya_perbaikan ?? 'NULL',
            $perbaikan->realisasi_biaya_perbaikan ?? 'NULL'
        );
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}