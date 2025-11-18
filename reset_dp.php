<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RESET DP KE 0 ===\n\n";

$tagihans = App\Models\TagihanOb::where('voyage', 'A10125')
    ->where('nama_supir', 'ABDULLAH')
    ->get();

foreach ($tagihans as $tagihan) {
    echo "ID {$tagihan->id}: {$tagihan->nomor_kontainer}\n";
    echo "DP sebelum: Rp " . number_format($tagihan->dp ?? 0, 0, ',', '.') . "\n";
    
    $tagihan->dp = 0;
    $tagihan->save();
    
    echo "DP sesudah: Rp 0\n";
    echo "---\n\n";
}

echo "Selesai! DP sudah di-reset ke 0.\n";
