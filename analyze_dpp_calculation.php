<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

echo "=== ANALISIS DETAIL PERHITUNGAN DPP ===\n";

// Ambil data container yang bermasalah
$container = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'TEXU7210230')
    ->where('periode', 6)
    ->first();

if ($container) {
    echo "Container: {$container->nomor_kontainer}\n";
    echo "Periode: {$container->periode}\n";
    echo "Masa: {$container->masa}\n";
    echo "DPP Saat Ini: Rp " . number_format($container->dpp, 0, ',', '.') . "\n\n";
    
    // Hitung jumlah hari aktual
    $startDate = \Carbon\Carbon::parse($container->tanggal_awal);
    $endDate = \Carbon\Carbon::parse($container->tanggal_akhir);
    $actualDays = $startDate->diffInDays($endDate) + 1;
    
    // Ambil master pricelist
    $pricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $container->size)
        ->where('vendor', $container->vendor)
        ->first();
    
    if ($pricelist) {
        echo "=== SIMULASI PERHITUNGAN YANG BENAR ===\n";
        $isBulanan = strtolower($pricelist->tarif) === 'bulanan';
        
        if ($isBulanan) {
            echo "Tarif: BULANAN\n";
            echo "Harga Master: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
            echo "Jumlah Hari: $actualDays hari\n";
            echo "DPP Benar = Rp " . number_format($pricelist->harga, 0, ',', '.') . " (TIDAK dikali hari)\n";
            
            $correctDpp = $pricelist->harga;
            $correctPpn = $correctDpp * 0.11;
            $correctPph = $correctDpp * 0.02; 
            $correctGrandTotal = $correctDpp + $correctPpn - $correctPph;
            
            echo "\nPerhitungan Benar:\n";
            echo "- DPP: Rp " . number_format($correctDpp, 0, ',', '.') . "\n";
            echo "- PPN (11%): Rp " . number_format($correctPpn, 0, ',', '.') . "\n";
            echo "- PPH (2%): Rp " . number_format($correctPph, 0, ',', '.') . "\n";
            echo "- Grand Total: Rp " . number_format($correctGrandTotal, 0, ',', '.') . "\n";
            
            echo "\nPerhitungan Saat Ini (SALAH):\n";
            echo "- DPP: Rp " . number_format($container->dpp, 0, ',', '.') . "\n";
            echo "- PPN: Rp " . number_format($container->ppn, 0, ',', '.') . "\n";
            echo "- PPH: Rp " . number_format($container->pph, 0, ',', '.') . "\n";
            echo "- Grand Total: Rp " . number_format($container->grand_total, 0, ',', '.') . "\n";
            
            echo "\nSelisih:\n";
            echo "- DPP: Rp " . number_format($container->dpp - $correctDpp, 0, ',', '.') . " (OVERSTATED)\n";
            echo "- Grand Total: Rp " . number_format($container->grand_total - $correctGrandTotal, 0, ',', '.') . " (OVERSTATED)\n";
            
        } else {
            echo "Tarif: HARIAN\n";
            echo "DPP = {$pricelist->harga} × $actualDays = " . ($pricelist->harga * $actualDays) . "\n";
        }
    }
    
    echo "\n=== KEMUNGKINAN PENYEBAB BUG ===\n";
    echo "1. System menggunakan 'periode' (angka 6) sebagai multiplier\n";
    echo "2. Atau ada kesalahan dalam deteksi isBulanan flag\n";
    echo "3. Atau ada default tarif harian yang digunakan\n";
    
    // Cek apakah ada bug dalam logic
    if ($container->dpp == ($pricelist->harga * $actualDays)) {
        echo "\n🔍 BUG CONFIRMED: System menghitung tarif bulanan × jumlah hari!\n";
    } else {
        $impliedDailyRate = $container->dpp / $actualDays;
        echo "\n🔍 ANALISIS: Implied daily rate = Rp " . number_format($impliedDailyRate, 0, ',', '.') . "\n";
        
        // Cek apakah menggunakan nilai periode sebagai multiplier
        $impliedByPeriode = $pricelist->harga * $container->periode;
        if (abs($container->dpp - $impliedByPeriode) < 100) {
            echo "🔍 BUG CONFIRMED: System menggunakan 'periode' sebagai multiplier!\n";
            echo "    Perhitungan: {$pricelist->harga} × {$container->periode} = " . number_format($impliedByPeriode, 0, ',', '.') . "\n";
        }
    }
}