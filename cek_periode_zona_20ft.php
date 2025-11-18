<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== CEK KONTAINER ZONA 20FT BERDASARKAN PERIODE ===\n\n";

// Cek distribusi periode
$distribusi = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->selectRaw('periode, COUNT(*) as jumlah')
    ->groupBy('periode')
    ->orderBy('periode')
    ->get();

echo "Distribusi berdasarkan periode:\n";
foreach ($distribusi as $item) {
    echo "Periode {$item->periode}: {$item->jumlah} kontainer\n";
}

echo "\n=== SAMPLE KONTAINER DENGAN PERIODE = 1 (SEBULAN) ===\n";
$sebulan = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->where('periode', 1)
    ->limit(10)
    ->get(['id', 'nomor_kontainer', 'periode', 'masa', 'dpp']);

foreach ($sebulan as $item) {
    echo sprintf(
        "ID: %d | Kontainer: %s | Periode: %d | Masa: %s | DPP: Rp %s\n",
        $item->id,
        $item->nomor_kontainer,
        $item->periode,
        $item->masa,
        number_format($item->dpp, 0, ',', '.')
    );
}

echo "\nTotal kontainer dengan periode = 1: " . $sebulan->count() . "\n";
