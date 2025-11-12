<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

$tagihan = DaftarTagihanKontainerSewa::find(5058);

echo "=== LAPORAN LENGKAP KESALAHAN DPP ===" . PHP_EOL;
echo "Kontainer: " . $tagihan->nomor_kontainer . " (Periode " . $tagihan->periode . ")" . PHP_EOL;
echo PHP_EOL;

echo "=== 1. DATA DI DATABASE ===" . PHP_EOL;
echo "Vendor: " . $tagihan->vendor . PHP_EOL;
echo "Ukuran: " . $tagihan->size . PHP_EOL;
echo "Tanggal: " . $tagihan->tanggal_awal->format('d M Y') . " - " . $tagihan->tanggal_akhir->format('d M Y') . PHP_EOL;
echo "Jumlah Hari Aktual: " . $tagihan->tanggal_awal->diffInDays($tagihan->tanggal_akhir) + 1 . " hari" . PHP_EOL;
echo "Periode (di database): " . $tagihan->periode . PHP_EOL;
echo "Tarif Type: " . $tagihan->tarif . PHP_EOL;
echo PHP_EOL;

echo "DPP di Database: Rp " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
echo "PPN di Database: Rp " . number_format($tagihan->ppn, 2, '.', ',') . PHP_EOL;
echo "Grand Total di Database: Rp " . number_format($tagihan->grand_total, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Cek master pricelist
echo "=== 2. MASTER PRICELIST ===" . PHP_EOL;
$pricelist = MasterPricelistSewaKontainer::where('vendor', $tagihan->vendor)
    ->where('ukuran_kontainer', $tagihan->size)
    ->first();

if ($pricelist) {
    echo "Vendor: " . $pricelist->vendor . PHP_EOL;
    echo "Ukuran: " . $pricelist->ukuran_kontainer . PHP_EOL;
    echo "Tarif: " . $pricelist->tarif . PHP_EOL;
    echo "Harga: Rp " . number_format($pricelist->harga, 2, '.', ',') . PHP_EOL;
    echo PHP_EOL;
    
    $jumlahHari = $tagihan->tanggal_awal->diffInDays($tagihan->tanggal_akhir) + 1;
    
    echo "=== 3. PERHITUNGAN YANG SEHARUSNYA ===" . PHP_EOL;
    if (strtolower($pricelist->tarif) === 'bulanan') {
        echo "Tarif Type: Bulanan" . PHP_EOL;
        echo "DPP Seharusnya: Rp " . number_format($pricelist->harga, 2, '.', ',') . " (tarif bulanan, tidak dikalikan hari)" . PHP_EOL;
        
        $dppSeharusnya = $pricelist->harga;
        $ppnSeharusnya = round($dppSeharusnya * 0.11, 2);
        $pphSeharusnya = round($dppSeharusnya * 0.02, 2);
        $grandTotalSeharusnya = $dppSeharusnya + $ppnSeharusnya - $pphSeharusnya;
        
        echo "PPN (11%): Rp " . number_format($ppnSeharusnya, 2, '.', ',') . PHP_EOL;
        echo "PPH (2%): Rp " . number_format($pphSeharusnya, 2, '.', ',') . PHP_EOL;
        echo "Grand Total: Rp " . number_format($grandTotalSeharusnya, 2, '.', ',') . PHP_EOL;
    } else {
        echo "Tarif Type: Harian" . PHP_EOL;
        echo "Tarif per Hari: Rp " . number_format($pricelist->harga, 2, '.', ',') . PHP_EOL;
        echo "Jumlah Hari: " . $jumlahHari . " hari" . PHP_EOL;
        echo "DPP Seharusnya: Rp " . number_format($pricelist->harga, 2, '.', ',') . " × " . $jumlahHari . " = Rp " . number_format($pricelist->harga * $jumlahHari, 2, '.', ',') . PHP_EOL;
        
        $dppSeharusnya = $pricelist->harga * $jumlahHari;
        $ppnSeharusnya = round($dppSeharusnya * 0.11, 2);
        $pphSeharusnya = round($dppSeharusnya * 0.02, 2);
        $grandTotalSeharusnya = $dppSeharusnya + $ppnSeharusnya - $pphSeharusnya;
        
        echo "PPN (11%): Rp " . number_format($ppnSeharusnya, 2, '.', ',') . PHP_EOL;
        echo "PPH (2%): Rp " . number_format($pphSeharusnya, 2, '.', ',') . PHP_EOL;
        echo "Grand Total: Rp " . number_format($grandTotalSeharusnya, 2, '.', ',') . PHP_EOL;
    }
    
    echo PHP_EOL;
    echo "=== 4. ANALISIS KESALAHAN ===" . PHP_EOL;
    echo "DPP Database: Rp " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
    echo "DPP Seharusnya: Rp " . number_format($dppSeharusnya, 2, '.', ',') . PHP_EOL;
    echo "Selisih DPP: Rp " . number_format(abs($tagihan->dpp - $dppSeharusnya), 2, '.', ',') . PHP_EOL;
    echo PHP_EOL;
    
    echo "Grand Total Database: Rp " . number_format($tagihan->grand_total, 2, '.', ',') . PHP_EOL;
    echo "Grand Total Seharusnya: Rp " . number_format($grandTotalSeharusnya, 2, '.', ',') . PHP_EOL;
    echo "Selisih Grand Total: Rp " . number_format(abs($tagihan->grand_total - $grandTotalSeharusnya), 2, '.', ',') . PHP_EOL;
    echo PHP_EOL;
    
    // Coba hitung mundur untuk tahu tarif apa yang dipakai
    echo "=== 5. REVERSE ENGINEERING ===" . PHP_EOL;
    echo "Dari DPP database (Rp " . number_format($tagihan->dpp, 2, '.', ',') . "), tarif yang digunakan:" . PHP_EOL;
    
    if ($jumlahHari > 0) {
        $tarifTerpakai = $tagihan->dpp / $jumlahHari;
        echo "Tarif per Hari = DPP / Jumlah Hari = Rp " . number_format($tagihan->dpp, 2, '.', ',') . " / " . $jumlahHari . " = Rp " . number_format($tarifTerpakai, 2, '.', ',') . PHP_EOL;
        echo "Tarif Seharusnya (dari pricelist): Rp " . number_format($pricelist->harga, 2, '.', ',') . PHP_EOL;
        echo "Selisih Tarif: Rp " . number_format(abs($tarifTerpakai - $pricelist->harga), 2, '.', ',') . PHP_EOL;
    }
    
    echo PHP_EOL;
    echo "=== KESIMPULAN ===" . PHP_EOL;
    echo "❌ DPP salah karena:" . PHP_EOL;
    
    if (strtolower($pricelist->tarif) === 'bulanan' && $tagihan->dpp != $pricelist->harga) {
        echo "   - Tarif bulanan (Rp " . number_format($pricelist->harga, 2, '.', ',') . ") dikalikan dengan hari" . PHP_EOL;
        echo "   - Seharusnya langsung Rp " . number_format($pricelist->harga, 2, '.', ',') . " tanpa dikalikan" . PHP_EOL;
    } else if (strtolower($pricelist->tarif) === 'harian') {
        $expectedDpp = $pricelist->harga * $jumlahHari;
        if ($tagihan->dpp < $expectedDpp) {
            echo "   - Kemungkinan menggunakan jumlah hari yang salah" . PHP_EOL;
            echo "   - Atau tarif per hari yang salah" . PHP_EOL;
        }
    }
    
    echo "   - Data mungkin diimport dari Excel dengan perhitungan yang salah" . PHP_EOL;
    echo "   - Atau ada bug dalam fungsi calculateFinancialData()" . PHP_EOL;
    
} else {
    echo "⚠️ Master pricelist tidak ditemukan untuk vendor " . $tagihan->vendor . " ukuran " . $tagihan->size . PHP_EOL;
}
