<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== CHECKING DATABASE RECORDS ===\n\n";

$records = DaftarTagihanKontainerSewa::latest()->take(10)->get(['id', 'vendor', 'nomor_kontainer', 'tanggal_awal', 'tanggal_akhir', 'created_at']);

echo "📋 Latest 10 records in database:\n";
echo str_repeat("-", 80) . "\n";

foreach ($records as $record) {
    echo sprintf("ID: %3d | %3s | %-12s | %10s to %10s | %s\n",
        $record->id,
        $record->vendor,
        $record->nomor_kontainer,
        $record->tanggal_awal,
        $record->tanggal_akhir,
        $record->created_at->format('Y-m-d H:i:s')
    );
}

echo str_repeat("-", 80) . "\n";
echo "Total records: " . DaftarTagihanKontainerSewa::count() . "\n";

// Check specific records from your CSV
echo "\n🔍 Checking specific records from your CSV...\n";

$csvRecords = [
    ['DPE', 'CCLU3836629', '2025-01-21'],
    ['DPE', 'CCLU3836629', '2025-02-21'],
    ['DPE', 'DPEU4869769', '2025-03-22'],
];

foreach ($csvRecords as [$vendor, $kontainer, $tanggal]) {
    $existing = DaftarTagihanKontainerSewa::where('vendor', $vendor)
                                          ->where('nomor_kontainer', $kontainer)
                                          ->where('tanggal_awal', $tanggal)
                                          ->first();

    if ($existing) {
        echo "✅ Found: {$vendor} - {$kontainer} - {$tanggal} (ID: {$existing->id})\n";
    } else {
        echo "❌ Not found: {$vendor} - {$kontainer} - {$tanggal}\n";
    }
}

echo "\n=== ANALYSIS COMPLETE ===\n";
