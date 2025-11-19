<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Now we can use Laravel
echo "=== Fixing Periode 5 for APZU3960241 ===\n";

$periode5 = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'APZU3960241')
    ->where('periode', 5)
    ->whereNull('tanggal_akhir')
    ->first();

if ($periode5) {
    echo "Found periode 5 with NULL tanggal_akhir:\n";
    echo "- ID: {$periode5->id}\n";
    echo "- Tanggal awal: {$periode5->tanggal_awal}\n";
    echo "- Tanggal akhir: " . ($periode5->tanggal_akhir ?? 'NULL') . "\n";
    
    // Update tanggal_akhir to container end date
    $periode5->tanggal_akhir = '2025-05-25';
    $periode5->save();
    
    echo "\n✅ UPDATED periode 5 tanggal_akhir to: 2025-05-25\n";
} else {
    echo "❌ Periode 5 with NULL tanggal_akhir not found\n";
}

echo "\nDone!\n";