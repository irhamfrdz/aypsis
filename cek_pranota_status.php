<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== CEK STATUS PRANOTA ===\n\n";

$pranotaCount = PranotaTagihanKontainerSewa::count();
echo "Total Pranota: $pranotaCount\n\n";

if ($pranotaCount > 0) {
    $pranota = PranotaTagihanKontainerSewa::first();
    echo "Pranota Pertama:\n";
    echo "- ID: {$pranota->id}\n";
    echo "- No Invoice: {$pranota->no_invoice}\n";
    echo "- Status: {$pranota->status}\n";
    echo "- Jumlah Tagihan: {$pranota->jumlah_tagihan}\n\n";
    
    // Get first tagihan
    $tagihan = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->first();
    if ($tagihan) {
        echo "Tagihan Pertama di Pranota:\n";
        echo "- ID: {$tagihan->id}\n";
        echo "- Nomor Kontainer: {$tagihan->nomor_kontainer}\n";
        echo "- Grand Total: Rp " . number_format($tagihan->grand_total, 2) . "\n";
    }
}
