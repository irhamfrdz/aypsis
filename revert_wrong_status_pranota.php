<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;

echo "Merevert status_pranota yang salah...\n\n";

$updated = 0;
$kept = 0;

// Ambil semua tagihan dengan status_pranota = 'sudah_dibayar'
$tagihans = DaftarTagihanKontainerSewa::where('status_pranota', 'sudah_dibayar')->get();

echo "Ditemukan {$tagihans->count()} tagihan dengan status 'sudah_dibayar'\n";

foreach ($tagihans as $tagihan) {
    // Cek apakah tagihan ini benar-benar ada di pranota
    $isInPranota = PranotaTagihanKontainerSewa::whereJsonContains('tagihan_kontainer_sewa_ids', (string)$tagihan->id)
        ->exists();
    
    if (!$isInPranota) {
        // Tidak ada di pranota, revert ke NULL
        $tagihan->status_pranota = null;
        $tagihan->save();
        
        $vendor = is_object($tagihan->vendor) ? $tagihan->vendor->nama : $tagihan->vendor;
        $kontainer = is_object($tagihan->kontainer) ? $tagihan->kontainer->nomor_kontainer : $tagihan->kontainer;
        
        echo "Revert #{$tagihan->id} - {$vendor} - {$kontainer} - Bank: {$tagihan->nomor_bank}\n";
        $updated++;
    } else {
        $kept++;
    }
}

echo "\n";
echo "Total diproses: {$tagihans->count()}\n";
echo "Direset ke NULL: {$updated}\n";
echo "Tetap 'sudah_dibayar' (benar-benar di pranota): {$kept}\n";
