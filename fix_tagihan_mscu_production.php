<?php
/**
 * Script untuk memperbaiki data tagihan kontainer sewa MSCU7196740
 * 
 * Script ini akan:
 * 1. Mencari semua record dengan nomor kontainer MSCU7196740
 * 2. Mengubah vendor menjadi ZONA dan size menjadi 40ft
 * 3. Menyesuaikan DPP dengan Master Pricelist ZONA 40ft
 * 4. Menghitung ulang PPN, PPH, dan Grand Total secara otomatis
 * 
 * Cara menjalankan:
 * php fix_tagihan_mscu_production.php
 */

require __DIR__.'/vendor/autoload.php';

// Inisialisasi aplikasi Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Support\Facades\DB;

// KONFIGURASI
$nomorKontainer = 'MSCU7196740';
$targetVendor = 'ZONA';
$targetSize = '40'; // Ukuran diperbaiki ke 40ft sesuai instruksi perbaikan teknis

echo "========================================================\n";
echo "🛠️  PERBAIKAN TAGIHAN KONTINER: $nomorKontainer\n";
echo "========================================================\n\n";

// 1. Ambil data tagihan
$tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->get();

if ($tagihans->isEmpty()) {
    echo "❌ Data kontainer $nomorKontainer tidak ditemukan di database.\n";
    exit(1);
}

echo "📦 Ditemukan " . $tagihans->count() . " record tagihan.\n";

// 2. Ambil harga dari Master Pricelist
$pricelist = MasterPricelistSewaKontainer::where('vendor', $targetVendor)
                ->where('ukuran_kontainer', $targetSize)
                ->where('tarif', 'Bulanan') // Asumsi tarif bulanan
                ->first();

if (!$pricelist) {
    // Coba cari tanpa filter tarif jika tidak ketemu
    $pricelist = MasterPricelistSewaKontainer::where('vendor', $targetVendor)
                    ->where('ukuran_kontainer', $targetSize)
                    ->first();
}

if (!$pricelist) {
    echo "❌ master_pricelist_sewa_kontainers tidak ditemukan untuk Vendor: $targetVendor, Size: $targetSize\n";
    exit(1);
}

$hargaBaru = $pricelist->harga;
echo "💰 Harga di Pricelist: Rp " . number_format($hargaBaru, 2, ',', '.') . "\n\n";

// 3. Eksekusi Perbaikan
echo "🔄 Sedang memproses perbaikan...\n\n";

try {
    DB::beginTransaction();

    foreach ($tagihans as $tagihan) {
        echo "Processing ID: {$tagihan->id} (Periode: {$tagihan->periode})...\n";
        
        $oldDpp = $tagihan->dpp;
        
        // --- PRO-RATING LOGIC ---
        // Parse dates from the record or its masa string
        $start = \Carbon\Carbon::parse($tagihan->tanggal_awal);
        $end = \Carbon\Carbon::parse($tagihan->tanggal_akhir);
        
        $daysInPeriod = $start->diffInDays($end) + 1;
        $daysInFullMonth = $start->daysInMonth;
        
        $isFullMonth = $daysInPeriod >= $daysInFullMonth;
        
        $calculatedDpp = $isFullMonth ? $hargaBaru : round($hargaBaru * ($daysInPeriod / $daysInFullMonth), 2);
        // ------------------------

        // Update data dasar
        $tagihan->vendor = $targetVendor;
        $tagihan->size = $targetSize;
        $tagihan->tarif = $isFullMonth ? 'Bulanan' : 'Harian';
        $tagihan->dpp = $calculatedDpp;
        
        // Model DaftarTagihanKontainerSewa memiliki boot method saving() 
        // yang secara otomatis memanggil calculateGrandTotal() dan recalculateTaxes().
        // Ini akan memastikan PPN, PPH, dan Grand Total terupdate dengan benar.
        $tagihan->save();
        
        echo "   📅 Masa: " . $tagihan->masa . " ($daysInPeriod/$daysInFullMonth days)\n";
        echo "   ✅ DPP: " . number_format($oldDpp, 2) . " -> " . number_format($tagihan->dpp, 2) . " (" . $tagihan->tarif . ")\n";
        echo "   ✅ PPN: " . number_format($tagihan->ppn, 2) . "\n";
        echo "   ✅ PPH: " . number_format($tagihan->pph, 2) . "\n";
        echo "   ✅ Grand Total: " . number_format($tagihan->grand_total, 2) . "\n\n";
    }

    DB::commit();
    echo "========================================================\n";
    echo "✅ SELESAI! Semua record telah diperbaiki.\n";
    echo "========================================================\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ TERJADI KESALAHAN: " . $e->getMessage() . "\n";
    exit(1);
}
