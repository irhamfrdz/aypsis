<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Cek Status Pranota untuk Tagihan dengan Nomor Bank\n";
echo str_repeat("=", 100) . "\n\n";

$tagihans = DaftarTagihanKontainerSewa::whereNotNull('nomor_bank')
    ->where('nomor_bank', '!=', '')
    ->take(10)
    ->get(['id', 'nomor_bank', 'status_pranota', 'pranota_tagihan_kontainer_sewa_id', 'vendor', 'kontainer']);

foreach ($tagihans as $tagihan) {
    $vendor = is_string($tagihan->vendor) ? $tagihan->vendor : 'N/A';
    $kontainer = is_string($tagihan->kontainer) ? $tagihan->kontainer : 'N/A';
    
    echo sprintf(
        "ID: %-5s | Vendor: %-10s | Nomor Bank: %-15s | Status Pranota: %-15s | Pranota ID: %s\n",
        $tagihan->id,
        substr($vendor, 0, 10),
        substr($tagihan->nomor_bank, 0, 15),
        substr($tagihan->status_pranota ?? 'NULL', 0, 15),
        $tagihan->pranota_tagihan_kontainer_sewa_id ?? 'NULL'
    );
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "Kesimpulan:\n";
echo "- Jika Status Pranota = 'sudah_dibayar' tapi Pranota ID = NULL\n";
echo "  Artinya: status_pranota sudah diupdate, tapi tagihan belum benar-benar masuk ke pranota\n";
echo "- Untuk benar-benar 'masuk pranota', tagihan harus memiliki pranota_tagihan_kontainer_sewa_id\n";
