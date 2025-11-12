<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== MENCARI KONTAINER MSKU22180 ===" . PHP_EOL;

$tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%MSKU22180%')
    ->get();

if ($tagihans->isEmpty()) {
    echo "Tidak ditemukan kontainer dengan nomor MSKU22180" . PHP_EOL;
    
    // Coba cari periode 4 saja
    echo PHP_EOL . "=== MENCARI SEMUA DATA PERIODE 4 YANG PPN-NYA 52.026 atau 52.027 ===" . PHP_EOL;
    $tagihans = DaftarTagihanKontainerSewa::where('periode', 4)
        ->where(function($q) {
            $q->where('ppn', 52026)
              ->orWhere('ppn', 52.026)
              ->orWhere('ppn', 52027)
              ->orWhere('ppn', 52.027);
        })
        ->get();
        
    if ($tagihans->isEmpty()) {
        echo "Tidak ditemukan data periode 4 dengan PPN 52.026 atau 52.027" . PHP_EOL;
    } else {
        foreach ($tagihans as $tagihan) {
            echo PHP_EOL . "---" . PHP_EOL;
            echo "ID: " . $tagihan->id . PHP_EOL;
            echo "Kontainer: " . $tagihan->nomor_kontainer . PHP_EOL;
            echo "Periode: " . $tagihan->periode . PHP_EOL;
            echo "DPP: " . $tagihan->dpp . PHP_EOL;
            echo "Adjustment: " . $tagihan->adjustment . PHP_EOL;
            echo "PPN (Database): " . $tagihan->ppn . PHP_EOL;
            
            $adjustedDpp = floatval($tagihan->dpp) + floatval($tagihan->adjustment);
            $ppnCalculated = $adjustedDpp * 0.11;
            $ppnRounded = round($ppnCalculated, 2);
            
            echo "Adjusted DPP: " . $adjustedDpp . PHP_EOL;
            echo "PPN (11% tanpa round): " . $ppnCalculated . PHP_EOL;
            echo "PPN (11% dengan round): " . $ppnRounded . PHP_EOL;
            echo "Selisih: " . ($tagihan->ppn - $ppnRounded) . PHP_EOL;
        }
    }
} else {
    foreach ($tagihans as $tagihan) {
        echo PHP_EOL . "---" . PHP_EOL;
        echo "ID: " . $tagihan->id . PHP_EOL;
        echo "Periode: " . $tagihan->periode . PHP_EOL;
        echo "DPP: " . $tagihan->dpp . PHP_EOL;
        echo "Adjustment: " . $tagihan->adjustment . PHP_EOL;
        echo "PPN (Database): " . $tagihan->ppn . PHP_EOL;
        
        $adjustedDpp = floatval($tagihan->dpp) + floatval($tagihan->adjustment);
        $ppnCalculated = $adjustedDpp * 0.11;
        $ppnRounded = round($ppnCalculated, 2);
        
        echo "Adjusted DPP: " . $adjustedDpp . PHP_EOL;
        echo "PPN (11% tanpa round): " . $ppnCalculated . PHP_EOL;
        echo "PPN (11% dengan round): " . $ppnRounded . PHP_EOL;
        echo "Selisih: " . ($tagihan->ppn - $ppnRounded) . PHP_EOL;
        
        if ($tagihan->periode == 4) {
            echo ">>> INI DATA PERIODE 4 <<<" . PHP_EOL;
        }
    }
}
