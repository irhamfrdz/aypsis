<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATE DP UNTUK TAGIHAN OB ===\n\n";

// Data yang benar dari database
$kapal = 'KM. ALEXINDO 1';
$voyage = 'A10125';
$supir = 'ABDULLAH';
$dpAmount = 50000; // 50 ribu sebagai test

echo "Mencari tagihan dengan:\n";
echo "Kapal: $kapal\n";
echo "Voyage: $voyage\n";
echo "Supir: $supir\n\n";

$tagihans = App\Models\TagihanOb::where('kapal', $kapal)
    ->where('voyage', $voyage)
    ->where('nama_supir', $supir)
    ->get();

echo "Ditemukan: " . $tagihans->count() . " tagihan\n\n";

foreach ($tagihans as $tagihan) {
    echo "ID: {$tagihan->id}\n";
    echo "Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "Biaya: Rp " . number_format($tagihan->biaya, 0, ',', '.') . "\n";
    echo "DP Sebelum: Rp " . number_format($tagihan->dp ?? 0, 0, ',', '.') . "\n";
    
    // Update DP
    $currentDp = $tagihan->dp ?? 0;
    $newDp = $currentDp + $dpAmount;
    
    $tagihan->dp = $newDp;
    $tagihan->save();
    
    // Verify
    $tagihan->refresh();
    
    echo "DP Sesudah: Rp " . number_format($tagihan->dp ?? 0, 0, ',', '.') . "\n";
    echo "Status: " . ($tagihan->dp == $newDp ? "✓ SUCCESS" : "✗ FAILED") . "\n";
    echo "---\n\n";
}

echo "Selesai! Silakan refresh halaman pranota-ob.\n";
