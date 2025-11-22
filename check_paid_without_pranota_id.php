<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== Cek Data dengan status_pranota='paid' tapi pranota_id NULL ===\n\n";

$items = DaftarTagihanKontainerSewa::where('status_pranota', 'paid')
    ->whereNull('pranota_id')
    ->get();

echo "Total items: " . $items->count() . "\n\n";

if ($items->count() > 0) {
    echo "Sample 10 data:\n";
    foreach ($items->take(10) as $item) {
        echo "ID: {$item->id} | Kontainer: {$item->nomor_kontainer} | Nomor Bank: {$item->nomor_bank} | Status Pranota: {$item->status_pranota} | Pranota ID: " . ($item->pranota_id ?? 'NULL') . "\n";
    }
}
