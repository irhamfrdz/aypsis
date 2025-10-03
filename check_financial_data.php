<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Checking financial data in database...\n\n";

$tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'CBHU3952697')
    ->orderBy('periode')
    ->get();

foreach ($tagihans as $tagihan) {
    echo "Container: {$tagihan->nomor_kontainer} - Periode {$tagihan->periode}\n";
    echo "  DPP: " . ($tagihan->dpp ?? 'NULL') . " (type: " . gettype($tagihan->dpp) . ")\n";
    echo "  Adjustment: " . ($tagihan->adjustment ?? 'NULL') . " (type: " . gettype($tagihan->adjustment) . ")\n";
    echo "  DPP Nilai Lain: " . ($tagihan->dpp_nilai_lain ?? 'NULL') . " (type: " . gettype($tagihan->dpp_nilai_lain) . ")\n";
    echo "  PPN: " . ($tagihan->ppn ?? 'NULL') . " (type: " . gettype($tagihan->ppn) . ")\n";
    echo "  PPH: " . ($tagihan->pph ?? 'NULL') . " (type: " . gettype($tagihan->pph) . ")\n";
    echo "  Grand Total: " . ($tagihan->grand_total ?? 'NULL') . " (type: " . gettype($tagihan->grand_total) . ")\n";
    echo "  Tarif: " . ($tagihan->tarif ?? 'NULL') . "\n";
    echo "\n";
}

// Check total records
$total = DaftarTagihanKontainerSewa::count();
echo "Total records: {$total}\n\n";

// Check records with zero financial values
$zeroFinancial = DaftarTagihanKontainerSewa::where('dpp', 0)
    ->orWhere('ppn', 0)
    ->orWhere('grand_total', 0)
    ->count();

echo "Records with zero financial values: {$zeroFinancial}\n";
