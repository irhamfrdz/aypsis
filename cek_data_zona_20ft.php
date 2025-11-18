<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== CEK DATA ZONA 20FT ===\n\n";

$data = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->orderBy('periode', 'desc')
    ->limit(10)
    ->get(['id', 'nomor_kontainer', 'periode', 'dpp', 'ppn', 'grand_total']);

echo "Total ZONA 20ft: " . DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->where('size', '20')->count() . "\n\n";

echo "Sample 10 data (sorted by periode DESC):\n";
echo str_repeat("-", 100) . "\n";
printf("%-5s %-20s %-10s %-15s %-15s %-15s\n", "ID", "No Kontainer", "Periode", "DPP", "PPN", "Grand Total");
echo str_repeat("-", 100) . "\n";

foreach ($data as $item) {
    printf("%-5s %-20s %-10s %-15s %-15s %-15s\n",
        $item->id,
        $item->nomor_kontainer,
        $item->periode . ' hari',
        'Rp ' . number_format($item->dpp, 0, ',', '.'),
        'Rp ' . number_format($item->ppn, 0, ',', '.'),
        'Rp ' . number_format($item->grand_total, 0, ',', '.')
    );
}

echo str_repeat("-", 100) . "\n";

// Cek berapa yang periode >= 30
$count30 = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->where('periode', '>=', 30)
    ->count();

echo "\nKontainer ZONA 20ft dengan periode >= 30 hari: $count30\n";

// Cek range periode
$min = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->where('size', '20')->min('periode');
$max = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->where('size', '20')->max('periode');

echo "Range periode: Min = $min hari, Max = $max hari\n";
