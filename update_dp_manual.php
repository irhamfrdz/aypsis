<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MANUAL DP UPDATE FOR TESTING ===\n\n";

// Cari tagihan OB dengan kapal A10125 dan supir ABDULLAH
$tagihans = App\Models\TagihanOb::where('kapal', 'A10125')
    ->where('nama_supir', 'ABDULLAH')
    ->get();

echo "Found " . $tagihans->count() . " tagihan(s)\n\n";

foreach ($tagihans as $tagihan) {
    echo "ID: {$tagihan->id}\n";
    echo "Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "Biaya: Rp " . number_format($tagihan->biaya, 0, ',', '.') . "\n";
    echo "DP Sekarang: Rp " . number_format($tagihan->dp ?? 0, 0, ',', '.') . "\n";
    
    // Update DP untuk testing (set manual)
    $newDp = 50000; // Contoh: 50ribu
    $tagihan->update(['dp' => $newDp]);
    
    echo "DP Setelah Update: Rp " . number_format($newDp, 0, ',', '.') . "\n";
    echo "Status Update: SUCCESS\n";
    echo "---\n\n";
}

echo "Done!\n";
