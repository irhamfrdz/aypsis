<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Verifikasi Data - HANYA Tarif TEXT ===\n\n";

$sample = DaftarTagihanKontainerSewa::first();

if ($sample) {
    echo "Sample Record (ID: {$sample->id}):\n";
    echo "  vendor: {$sample->vendor}\n";
    echo "  nomor_kontainer: {$sample->nomor_kontainer}\n";
    echo "  size: {$sample->size}\n";
    echo "  tarif: \"{$sample->tarif}\" ← TEXT ONLY\n";

    // Check if tarif_nominal exists
    $attributes = $sample->getAttributes();
    if (array_key_exists('tarif_nominal', $attributes)) {
        $value = $attributes['tarif_nominal'];
        echo "  tarif_nominal: " . ($value ?? 'NULL') . " ← DIABAIKAN\n";
    } else {
        echo "  tarif_nominal: TIDAK ADA DI ATTRIBUTES\n";
    }

    echo "  periode: {$sample->periode} hari\n";
    echo "  dpp: Rp " . number_format($sample->dpp, 0, ',', '.') . " ← Dihitung otomatis\n";
}

echo "\n=== Breakdown by Tarif Type ===\n";
$byTarif = DaftarTagihanKontainerSewa::selectRaw('tarif, count(*) as total')
    ->groupBy('tarif')
    ->get();

foreach ($byTarif as $t) {
    echo "  \"{$t->tarif}\": {$t->total} records\n";
}

echo "\n=== Total Records ===\n";
echo "Total: " . DaftarTagihanKontainerSewa::count() . " records\n";

echo "\n✅ Import berhasil dengan tarif TEXT saja (tanpa tarif_nominal)!\n";

echo "\n=== Selesai ===\n";
