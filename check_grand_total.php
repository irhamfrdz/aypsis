<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'DNAU2622206')
    ->where('periode', '4')
    ->first();

if ($tagihan) {
    echo "Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "Vendor: {$tagihan->vendor}\n";
    echo "Periode: {$tagihan->periode}\n";
    echo "Grand Total: Rp " . number_format($tagihan->grand_total, 2, ',', '.') . "\n";
    echo "Updated at: {$tagihan->updated_at}\n";
} else {
    echo "Tagihan tidak ditemukan\n";
}
