1<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$noVoyage = 'SR10BJ26';
$bls = \App\Models\Manifest::where('no_voyage', $noVoyage)->get();

echo "Total manifests for voyage $noVoyage: ".$bls->count()."\n\n";

$headers = ['ID', 'BL', 'Container', 'Size', 'Type', 'Goods', 'Classification'];
echo sprintf("%-8s | %-10s | %-15s | %-5s | %-5s | %-30s | %-20s\n", ...$headers);
echo str_repeat('-', 110)."\n";

foreach ($bls as $item) {
    $isCargo = ($item->tipe_kontainer === 'CARGO' || empty($item->size_kontainer));
    $barangUpper = strtoupper($item->nama_barang ?? '');
    $isEmpty = str_contains($barangUpper, 'EMPTY') || ($item->tipe_kontainer == 'FCL' && (empty($item->nomor_kontainer) || str_starts_with($item->nomor_kontainer, 'CARGO-')));
    $size = trim(str_ireplace(['ft', 'feet', ' '], '', $item->size_kontainer ?? ''));
    if (empty($size)) {
        $size = '20';
    }
    $status = $isEmpty ? 'empty' : 'full';

    if ($isCargo) {
        $classification = 'CARGO';
    } else {
        $classification = "CONTAINER {$size}ft {$status}";
    }

    $goods = str_replace(["\r", "\n"], ' ', substr($item->nama_barang ?? '', 0, 28));

    echo sprintf(
        "%-8s | %-10s | %-15s | %-5s | %-5s | %-30s | %-20s\n",
        $item->id,
        $item->nomor_bl ?? '',
        $item->nomor_kontainer ?? '',
        $item->size_kontainer ?? '',
        $item->tipe_kontainer ?? '',
        $goods,
        $classification
    );
}
