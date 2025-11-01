<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SuratJalan;

echo "=== DEBUG SURAT JALAN TERM DATA ===\n\n";

// Check surat jalan data
echo "Checking SuratJalan data:\n";
$suratJalans = SuratJalan::take(10)->get();
foreach ($suratJalans as $sj) {
    echo "SuratJalan ID: {$sj->id}\n";
    echo "No Surat Jalan: {$sj->no_surat_jalan}\n";
    echo "Term: " . ($sj->term ?? 'NULL') . "\n";
    echo "Status: {$sj->status}\n";
    echo "---\n";
}

echo "\n=== END DEBUG ===\n";