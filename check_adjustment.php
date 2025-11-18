<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING ADJUSTMENTS IN DATABASE ===\n\n";

// Ambil semua data yang memiliki adjustment tidak 0
$tagihanWithAdjustment = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('adjustment', '!=', 0)
    ->orderBy('updated_at', 'desc')
    ->limit(10)
    ->get();

if ($tagihanWithAdjustment->count() > 0) {
    echo "Ditemukan {$tagihanWithAdjustment->count()} data dengan adjustment:\n\n";
    
    foreach ($tagihanWithAdjustment as $tagihan) {
        echo "ID: {$tagihan->id}\n";
        echo "Container: {$tagihan->nomor_kontainer}\n";
        echo "DPP: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
        echo "Adjustment: Rp " . number_format($tagihan->adjustment, 0, ',', '.') . "\n";
        echo "DPP + Adjustment: Rp " . number_format($tagihan->dpp + $tagihan->adjustment, 0, ',', '.') . "\n";
        echo "PPN: Rp " . number_format($tagihan->ppn, 0, ',', '.') . "\n";
        echo "PPH: Rp " . number_format($tagihan->pph, 0, ',', '.') . "\n";
        echo "Grand Total: Rp " . number_format($tagihan->grand_total, 0, ',', '.') . "\n";
        echo "Last Updated: {$tagihan->updated_at}\n";
        echo "\n---\n\n";
    }
} else {
    echo "Tidak ada data dengan adjustment.\n";
    echo "Silakan edit adjustment pada salah satu tagihan terlebih dahulu.\n";
}

echo "=== SELESAI ===\n";
