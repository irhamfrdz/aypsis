<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking TagihanOb data for kapal A10125...\n\n";

$tagihans = App\Models\TagihanOb::where('kapal', 'A10125')
    ->where('nama_supir', 'ABDULLAH')
    ->get(['id', 'kapal', 'voyage', 'nama_supir', 'nomor_kontainer', 'biaya', 'dp', 'updated_at']);

foreach ($tagihans as $tagihan) {
    echo "ID: {$tagihan->id}\n";
    echo "Kapal: {$tagihan->kapal}\n";
    echo "Voyage: {$tagihan->voyage}\n";
    echo "Supir: {$tagihan->nama_supir}\n";
    echo "Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "Biaya: Rp " . number_format($tagihan->biaya, 0, ',', '.') . "\n";
    echo "DP: Rp " . number_format($tagihan->dp ?? 0, 0, ',', '.') . "\n";
    echo "Updated: {$tagihan->updated_at}\n";
    echo "---\n\n";
}

echo "Total records: " . $tagihans->count() . "\n";
