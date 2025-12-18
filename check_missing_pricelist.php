<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECK MISSING PRICELIST ===\n\n";

// Get all unique vendor+size+tarif combinations from tagihan
$combinations = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('vendor', 'size', 'tarif')
    ->whereNotLike('nomor_kontainer', 'GROUP_SUMMARY_%')
    ->whereNotLike('nomor_kontainer', 'GROUP_TEMPLATE%')
    ->groupBy('vendor', 'size', 'tarif')
    ->orderBy('vendor')
    ->orderBy('size')
    ->orderBy('tarif')
    ->get();

echo "Unique combinations in tagihan: " . $combinations->count() . "\n\n";

// Get all pricelist
$pricelists = DB::table('master_pricelist_sewa_kontainers')->get();

echo "Pricelist available: " . $pricelists->count() . "\n\n";

// Check missing
$missing = [];
foreach ($combinations as $combo) {
    $found = false;
    foreach ($pricelists as $pricelist) {
        if (strtoupper($combo->vendor) == strtoupper($pricelist->vendor) &&
            $combo->size == $pricelist->ukuran_kontainer &&
            strtoupper($combo->tarif) == strtoupper($pricelist->tarif)) {
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $missing[] = $combo;
    }
}

if (!empty($missing)) {
    echo "MISSING PRICELIST:\n";
    echo str_repeat("─", 50) . "\n";
    printf("%-15s %-10s %-10s\n", "VENDOR", "SIZE", "TARIF");
    echo str_repeat("─", 50) . "\n";
    
    foreach ($missing as $m) {
        printf("%-15s %-10s %-10s\n", $m->vendor, $m->size, $m->tarif);
        
        // Count how many tagihan affected
        $count = DB::table('daftar_tagihan_kontainer_sewa')
            ->where('vendor', $m->vendor)
            ->where('size', $m->size)
            ->where('tarif', $m->tarif)
            ->count();
        echo "  → Affected tagihan: {$count} records\n\n";
    }
    
    echo "\n";
    echo "SUGGESTION: Add these to master_pricelist_sewa_kontainers:\n\n";
    foreach ($missing as $m) {
        echo "INSERT INTO master_pricelist_sewa_kontainers \n";
        echo "(vendor, tarif, ukuran_kontainer, harga, tanggal_harga_awal, created_at, updated_at)\n";
        echo "VALUES \n";
        echo "('{$m->vendor}', '{$m->tarif}', '{$m->size}', 0.00, '2025-01-01', NOW(), NOW());\n\n";
    }
} else {
    echo "✓ All combinations have pricelist!\n";
}
