<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;

echo "=== DEBUG RECALCULATE INKU6744298 ===\n\n";

// Ambil data tagihan
$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'INKU6744298')
    ->where('periode', 6)
    ->first();

echo "📋 DATA TAGIHAN:\n";
echo "Kontainer: {$tagihan->nomor_kontainer}\n";
echo "Periode: {$tagihan->periode}\n";
echo "Tarif: {$tagihan->tarif}\n";
echo "Size: {$tagihan->size}\n";
echo "Vendor: {$tagihan->vendor}\n";
echo "Tanggal Awal: {$tagihan->tanggal_awal}\n";
echo "DPP Saat Ini: " . number_format((float)$tagihan->dpp, 2) . "\n\n";

// Simulasi logic recalculate
$tarifType = $tagihan->tarif;
$periode = $tagihan->periode;
$size = $tagihan->size;
$vendor = $tagihan->vendor;
$tagihanDate = Carbon::parse($tagihan->tanggal_awal);

echo "🔍 PENCARIAN MASTER PRICELIST:\n";
echo "Ukuran: {$size}\n";
echo "Vendor: {$vendor}\n";
echo "Tarif: {$tarifType}\n";
echo "Tanggal Tagihan: {$tagihanDate->format('Y-m-d')}\n\n";

// Cari master pricelist dengan tanggal efektif
$masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
    ->where('vendor', $vendor)
    ->where('tarif', $tarifType)
    ->where('tanggal_harga_awal', '<=', $tagihanDate)
    ->orderBy('tanggal_harga_awal', 'desc')
    ->first();

if ($masterPricelist) {
    echo "✅ MASTER PRICELIST DITEMUKAN:\n";
    echo "ID: {$masterPricelist->id}\n";
    echo "Harga: " . number_format((float)$masterPricelist->harga, 2) . "\n";
    echo "Tanggal Berlaku: {$masterPricelist->tanggal_harga_awal}\n\n";

    // Hitung DPP yang benar
    if ($tarifType === 'Harian') {
        // Hitung hari
        if ($tagihan->masa && strpos($tagihan->masa, ' - ') !== false) {
            $parts = explode(' - ', $tagihan->masa);
            if (count($parts) === 2) {
                $startDate = Carbon::parse($parts[0]);
                $endDate = Carbon::parse($parts[1]);
                $days = $startDate->diffInDays($endDate) + 1;
                echo "Hari dari masa: {$days}\n";
            }
        }

        // Hitung dari tanggal database
        $startDate = Carbon::parse($tagihan->tanggal_awal);
        $endDate = Carbon::parse($tagihan->tanggal_akhir);
        $daysByDate = $startDate->diffInDays($endDate) + 1;
        echo "Hari dari database: {$daysByDate}\n";

        $jumlahHari = $daysByDate; // Gunakan database
        $newDpp = (float)$masterPricelist->harga * $jumlahHari;

        echo "\n🧮 PERHITUNGAN BARU:\n";
        echo "Harga per hari: " . number_format((float)$masterPricelist->harga, 2) . "\n";
        echo "Jumlah hari: {$jumlahHari}\n";
        echo "DPP Baru: " . number_format($newDpp, 2) . "\n";
        echo "DPP Lama: " . number_format((float)$tagihan->dpp, 2) . "\n";
        echo "Selisih: " . number_format(abs($newDpp - (float)$tagihan->dpp), 2) . "\n";

        if (abs($newDpp - (float)$tagihan->dpp) > 0.01) {
            echo "❌ PERLU UPDATE!\n";
        } else {
            echo "✅ TIDAK PERLU UPDATE\n";
        }
    }
} else {
    echo "❌ MASTER PRICELIST TIDAK DITEMUKAN!\n";

    // Cek semua pricelist yang ada
    echo "\n🔍 SEMUA PRICELIST ZONA 40 HARIAN:\n";
    $allPricelist = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
        ->where('ukuran_kontainer', '40')
        ->where('tarif', 'Harian')
        ->get();

    foreach ($allPricelist as $pl) {
        echo "ID: {$pl->id} | Harga: " . number_format((float)$pl->harga, 2) . " | Berlaku: {$pl->tanggal_harga_awal}\n";
    }
}

echo "\n=== SELESAI ===\n";
