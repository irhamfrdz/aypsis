<?php
/**
 * Debug script untuk mengecek apakah adjustment tersimpan
 */
require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== DEBUGGING ADJUSTMENT IMPORT ===\n\n";

// Cek data terbaru
echo "1. Data terbaru dari import:\n";
$recent = DaftarTagihanKontainerSewa::orderBy('created_at', 'desc')
    ->take(10)
    ->get(['id', 'vendor', 'nomor_kontainer', 'adjustment', 'created_at']);

foreach ($recent as $r) {
    echo "ID: {$r->id} | {$r->vendor} | {$r->nomor_kontainer} | Adj: " . ($r->adjustment ?? 'NULL') . " | {$r->created_at}\n";
}

echo "\n2. Data dengan adjustment bukan nol:\n";
$withAdjustment = DaftarTagihanKontainerSewa::whereNotNull('adjustment')
    ->where('adjustment', '!=', 0)
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get(['id', 'vendor', 'nomor_kontainer', 'adjustment']);

if ($withAdjustment->count() > 0) {
    foreach ($withAdjustment as $adj) {
        echo "ID: {$adj->id} | {$adj->vendor} | {$adj->nomor_kontainer} | Adj: {$adj->adjustment}\n";
    }
} else {
    echo "TIDAK ADA DATA dengan adjustment bukan nol!\n";
}

echo "\n3. Total records dengan adjustment:\n";
$totalWithAdj = DaftarTagihanKontainerSewa::whereNotNull('adjustment')
    ->where('adjustment', '!=', 0)
    ->count();
echo "Total: {$totalWithAdj} records\n";

echo "\n4. Sample data ZONA terbaru:\n";
$zonaSample = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get(['id', 'nomor_kontainer', 'adjustment', 'tarif']);

foreach ($zonaSample as $zona) {
    echo "ZONA | {$zona->nomor_kontainer} | Adj: " . ($zona->adjustment ?? 'NULL') . " | Tarif: " . ($zona->tarif ?? 'NULL') . "\n";
}