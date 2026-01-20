<?php
/**
 * Script untuk mengubah vendor dan size SEMUA kontainer MSCU7196740
 * - Vendor: ZONA
 * - Size: 40ft
 * - DPP = Harga pricelist per bulan (sama untuk semua periode)
 * 
 * Jalankan dengan: php update_vendor_mscu7196740.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Script Update SEMUA Kontainer MSCU7196740\n";
echo "- Vendor: ZONA\n";
echo "- Size: 40ft\n";
echo "- DPP: SAMA untuk semua periode\n";
echo "===========================================\n\n";

// 1. Cari SEMUA kontainer MSCU7196740
$nomorKontainer = 'MSCU7196740';
$vendorBaru = 'ZONA';
$sizeBaru = '40';  // Ubah size menjadi 40ft

$tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
    ->orderBy('periode')
    ->get();

if ($tagihans->isEmpty()) {
    echo "❌ Kontainer {$nomorKontainer} tidak ditemukan!\n";
    exit(1);
}

echo "📦 Ditemukan " . $tagihans->count() . " record kontainer {$nomorKontainer}\n\n";

// Tampilkan data saat ini
echo "📋 Data Saat Ini:\n";
foreach ($tagihans as $tagihan) {
    echo "   - Periode {$tagihan->periode}: Vendor={$tagihan->vendor}, Size={$tagihan->size}, DPP=Rp " . number_format($tagihan->dpp ?? 0, 0, ',', '.') . "\n";
}
echo "\n";

// 2. Cari pricelist ZONA untuk ukuran 40 dan tarif Bulanan
$pricelist = MasterPricelistSewaKontainer::where('vendor', $vendorBaru)
    ->where('ukuran_kontainer', $sizeBaru)
    ->where('tarif', 'Bulanan')
    ->first();

if (!$pricelist) {
    echo "❌ Pricelist tidak ditemukan untuk:\n";
    echo "   - Vendor: {$vendorBaru}\n";
    echo "   - Ukuran: {$sizeBaru}\n";
    echo "   - Tarif: Bulanan\n";
    exit(1);
}

echo "💰 Pricelist ZONA 40ft Bulanan:\n";
echo "   - ID: {$pricelist->id}\n";
echo "   - Vendor: {$pricelist->vendor}\n";
echo "   - Tarif: {$pricelist->tarif}\n";
echo "   - Ukuran: {$pricelist->ukuran_kontainer}\n";
echo "   - Harga: Rp " . number_format($pricelist->harga, 2, ',', '.') . " per bulan\n\n";

// DPP = Harga per bulan (SAMA untuk semua periode)
$dppPerBulan = (float) $pricelist->harga;

echo "📊 DPP untuk SEMUA periode: Rp " . number_format($dppPerBulan, 2, ',', '.') . "\n\n";

echo "===========================================\n";
echo "Memproses Update...\n";
echo "===========================================\n\n";

$successCount = 0;
$errorCount = 0;

try {
    DB::beginTransaction();
    
    foreach ($tagihans as $tagihan) {
        // DPP sama untuk semua periode (harga per bulan)
        $dpp = $dppPerBulan;
        
        // Update data
        $tagihan->vendor = $vendorBaru;
        $tagihan->size = $sizeBaru;
        $tagihan->dpp = $dpp;
        // dpp_nilai_lain, ppn, pph, grand_total akan otomatis dihitung oleh model
        $tagihan->save();
        
        $successCount++;
        echo "✅ Periode {$tagihan->periode}: DPP = Rp " . number_format($tagihan->dpp, 0, ',', '.') . 
             " | Grand Total = Rp " . number_format($tagihan->grand_total, 0, ',', '.') . "\n";
    }
    
    DB::commit();
    
    echo "\n===========================================\n";
    echo "✅ UPDATE BERHASIL!\n";
    echo "===========================================\n";
    echo "   - Total record diupdate: {$successCount}\n";
    echo "   - Total record error: {$errorCount}\n\n";
    
    // Tampilkan summary setelah update
    $tagihansUpdated = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
        ->orderBy('periode')
        ->get();
    
    $totalDpp = $tagihansUpdated->sum('dpp');
    $totalPpn = $tagihansUpdated->sum('ppn');
    $totalPph = $tagihansUpdated->sum('pph');
    $totalGrandTotal = $tagihansUpdated->sum('grand_total');
    
    echo "📊 Ringkasan Setelah Update:\n";
    echo "   - DPP per periode: Rp " . number_format($dppPerBulan, 2, ',', '.') . "\n";
    echo "   - Total DPP ({$successCount} periode): Rp " . number_format($totalDpp, 2, ',', '.') . "\n";
    echo "   - Total PPN: Rp " . number_format($totalPpn, 2, ',', '.') . "\n";
    echo "   - Total PPH: Rp " . number_format($totalPph, 2, ',', '.') . "\n";
    echo "   - Total Grand Total: Rp " . number_format($totalGrandTotal, 2, ',', '.') . "\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    exit(1);
}

echo "===========================================\n";
echo "Script selesai dijalankan!\n";
echo "===========================================\n";
