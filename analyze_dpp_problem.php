<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;

echo "=== ANALISIS MASALAH DPP INKU6744298 ===\n\n";

// 1. Data tagihan
$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'INKU6744298')
    ->where('periode', 6)
    ->first();

// 2. Master pricelist
$masterHarian = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
    ->where('ukuran_kontainer', '40')
    ->where('tarif', 'Harian')
    ->first();

echo "💡 TEMUAN MASALAH:\n";
echo "1. Master Pricelist Harian: Rp " . number_format((float)$masterHarian->harga, 0, ',', '.') . "\n";
echo "2. DPP Saat Ini: Rp " . number_format((float)$tagihan->dpp, 0, ',', '.') . "\n";
echo "3. Tanggal Efektif Master: {$masterHarian->tanggal_harga_awal}\n";
echo "4. Tanggal Tagihan: {$tagihan->tanggal_awal} s/d {$tagihan->tanggal_akhir}\n\n";

// 3. Cek apakah masalah pada tanggal efektif
$tagihanStart = Carbon::parse($tagihan->tanggal_awal);
$masterEffective = Carbon::parse($masterHarian->tanggal_harga_awal);

echo "🗓️ ANALISIS TANGGAL:\n";
echo "Tanggal Tagihan: {$tagihanStart->format('d-m-Y')}\n";
echo "Tanggal Efektif Master: {$masterEffective->format('d-m-Y')}\n";

if ($tagihanStart->lt($masterEffective)) {
    echo "❌ MASALAH DITEMUKAN: Tanggal tagihan LEBIH LAMA dari tanggal efektif master pricelist!\n";
    echo "Sistema mungkin menggunakan pricelist lama!\n\n";

    // Cari pricelist yang berlaku untuk tanggal tagihan
    echo "🔍 MENCARI PRICELIST YANG BERLAKU:\n";
    $oldPricelist = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
        ->where('ukuran_kontainer', '40')
        ->where('tarif', 'Harian')
        ->where('tanggal_harga_awal', '<=', $tagihanStart)
        ->orderBy('tanggal_harga_awal', 'desc')
        ->first();

    if ($oldPricelist) {
        echo "Pricelist yang berlaku: Rp " . number_format((float)$oldPricelist->harga, 0, ',', '.') . "\n";
        echo "Tanggal berlaku: {$oldPricelist->tanggal_harga_awal}\n";

        $days = 20;
        $calculatedDPP = (float)$oldPricelist->harga * $days;
        echo "Perhitungan: Rp " . number_format((float)$oldPricelist->harga, 0, ',', '.') . " × {$days} hari = Rp " . number_format($calculatedDPP, 0, ',', '.') . "\n";

        if (abs($calculatedDPP - (float)$tagihan->dpp) < 1) {
            echo "✅ COCOK! Sistem menggunakan pricelist yang berlaku untuk tanggal tersebut!\n";
        } else {
            echo "❌ Masih tidak cocok...\n";
        }
    } else {
        echo "❌ Tidak ada pricelist yang berlaku untuk tanggal tersebut!\n";
    }
} else {
    echo "✅ Tanggal tagihan sesuai dengan pricelist yang berlaku.\n";
}

echo "\n";

// 4. Perhitungan hari yang benar
echo "📏 PERHITUNGAN HARI YANG BENAR:\n";
$start = Carbon::parse($tagihan->tanggal_awal);
$end = Carbon::parse($tagihan->tanggal_akhir);

echo "Dari field 'masa': 5 Dec 2024 - 23 Dec 2024\n";
$masaStart = Carbon::parse('2024-12-05');
$masaEnd = Carbon::parse('2024-12-23');
$masaDays = $masaStart->diffInDays($masaEnd) + 1;
echo "Jumlah hari berdasarkan 'masa': {$masaDays} hari\n";

echo "\nDari tanggal database: {$start->format('d-m-Y')} - {$end->format('d-m-Y')}\n";
$dbDays = $start->diffInDays($end) + 1;
echo "Jumlah hari berdasarkan database: {$dbDays} hari\n";

echo "\n📊 KEMUNGKINAN PERHITUNGAN:\n";
if ($masaDays == 19) {
    $dppWith19 = (float)$tagihan->dpp / 19;
    echo "Jika 19 hari: Rp " . number_format($dppWith19, 2, ',', '.') . " per hari\n";
}

if ($dbDays == 20) {
    $dppWith20 = (float)$tagihan->dpp / 20;
    echo "Jika 20 hari: Rp " . number_format($dppWith20, 2, ',', '.') . " per hari\n";
}

// 5. Cek semua pricelist ZONA 40 untuk melihat history
echo "\n📋 HISTORY PRICELIST ZONA 40 HARIAN:\n";
$allPricelist = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
    ->where('ukuran_kontainer', '40')
    ->where('tarif', 'Harian')
    ->orderBy('tanggal_harga_awal')
    ->get();

foreach ($allPricelist as $pl) {
    echo "Harga: Rp " . number_format((float)$pl->harga, 0, ',', '.') . " | Berlaku: {$pl->tanggal_harga_awal}\n";
}

echo "\n=== KESIMPULAN ===\n";
