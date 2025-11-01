<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Prospek;
use App\Models\Bl;
use App\Models\TandaTerima;

echo "=== DEBUG TERM DATA ===\n\n";

// Check tanda terima data
echo "1. Checking TandaTerima data:\n";
$tandaTerimas = TandaTerima::take(5)->get();
foreach ($tandaTerimas as $tt) {
    echo "TandaTerima ID: {$tt->id}\n";
    echo "Term: " . ($tt->term ?? 'NULL') . "\n";
    echo "No Surat Jalan: " . ($tt->no_surat_jalan ?? 'NULL') . "\n";
    echo "---\n";
}

// Check prospek with tanda terima
echo "\n2. Checking Prospek with Tanda Terima:\n";
$prospeks = Prospek::with('tandaTerima')
    ->whereNotNull('tanda_terima_id')
    ->take(5)
    ->get();

foreach ($prospeks as $prospek) {
    echo "Prospek ID: {$prospek->id}\n";
    echo "Tanda Terima ID: {$prospek->tanda_terima_id}\n";
    echo "Tanda Terima Term: " . ($prospek->tandaTerima ? $prospek->tandaTerima->term : 'NULL') . "\n";
    echo "---\n";
}

// Check BL data
echo "\n3. Checking BL data:\n";
$bls = Bl::take(5)->get();
foreach ($bls as $bl) {
    echo "BL ID: {$bl->id}\n";
    echo "Prospek ID: {$bl->prospek_id}\n";
    echo "Term: " . ($bl->term ?? 'NULL') . "\n";
    echo "---\n";
}

echo "\n=== END DEBUG ===\n";