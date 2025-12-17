<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verifikasi Hasil Update Kolom Masa ===\n\n";

// Cek RXTU4540180
$rxtu = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'RXTU4540180')
    ->whereNull('status_pranota')
    ->orderBy('periode')
    ->limit(10)
    ->get(['periode', 'masa', 'tanggal_awal', 'tanggal_akhir', 'size', 'tarif']);

echo "Kontainer RXTU4540180 (dari screenshot):\n";
echo str_repeat("-", 80) . "\n";
foreach ($rxtu as $r) {
    echo "Periode {$r->periode}: {$r->tanggal_awal} - {$r->tanggal_akhir}\n";
    echo "  Masa: '{$r->masa}'\n";
    echo "  Size: {$r->size} | Tarif: {$r->tarif}\n\n";
}

// Cek beberapa kontainer lainnya
echo "\n=== Sample Kontainer Lain ===\n\n";
$others = DB::table('daftar_tagihan_kontainer_sewa')
    ->whereNull('status_pranota')
    ->where('created_at', '>', now()->subMinutes(10))
    ->limit(5)
    ->get(['nomor_kontainer', 'periode', 'masa', 'tanggal_awal', 'tanggal_akhir']);

foreach ($others as $o) {
    echo "{$o->nomor_kontainer} - Periode {$o->periode}\n";
    echo "  Masa: '{$o->masa}'\n";
    echo "  Range: {$o->tanggal_awal} - {$o->tanggal_akhir}\n\n";
}

echo "=== SELESAI ===\n";
