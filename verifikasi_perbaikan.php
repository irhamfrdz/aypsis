<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Events\EventServiceProvider;
use App\Models\DaftarTagihanKontainerSewa;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFIKASI HASIL PERBAIKAN ===\n\n";

// Cek beberapa contoh yang sudah diperbaiki
$samples = [
    ['nomor_kontainer' => 'MSCU7085120', 'periode' => 6],
    ['nomor_kontainer' => 'APZU3960241', 'periode' => 5],
    ['nomor_kontainer' => 'BMOU4192536', 'periode' => 16]
];

foreach ($samples as $sample) {
    $tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', $sample['nomor_kontainer'])
        ->where('periode', $sample['periode'])
        ->first();

    if ($tagihan) {
        echo "✅ {$tagihan->nomor_kontainer} Periode {$tagihan->periode}:\n";
        echo "   Masa: {$tagihan->masa}\n";
        echo "   DPP: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
        echo "   Tarif: {$tagihan->tarif}\n";
        echo "   Vendor: {$tagihan->vendor}, Size: {$tagihan->size}\n\n";
    }
}

// Hitung total kontainer dengan tarif harian
$totalHarian = DaftarTagihanKontainerSewa::where('tarif', 'Harian')->count();
echo "📊 Total kontainer dengan tarif harian: {$totalHarian}\n";

echo "\n=== VERIFIKASI SELESAI ===\n";
