<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING ALL TAGIHAN OB DATA ===\n\n";

// Cari semua tagihan OB dengan kapal yang mengandung A10125
$tagihans = App\Models\TagihanOb::where('kapal', 'LIKE', '%A10125%')
    ->orWhere('voyage', 'LIKE', '%A10125%')
    ->get();

echo "Found " . $tagihans->count() . " tagihan(s)\n\n";

foreach ($tagihans as $tagihan) {
    echo "ID: {$tagihan->id}\n";
    echo "Kapal: [{$tagihan->kapal}]\n";
    echo "Voyage: [{$tagihan->voyage}]\n";
    echo "Supir: [{$tagihan->nama_supir}]\n";
    echo "Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "Biaya: Rp " . number_format($tagihan->biaya, 0, ',', '.') . "\n";
    echo "DP: Rp " . number_format($tagihan->dp ?? 0, 0, ',', '.') . "\n";
    echo "---\n\n";
}

echo "\nDone!\n";
