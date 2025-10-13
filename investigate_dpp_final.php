<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;

echo "=== INVESTIGASI DPP INKU6744298 PERIODE 6 ===\n\n";

// 1. Ambil data tagihan
$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'INKU6744298')
    ->where('periode', 6)
    ->first();

if (!$tagihan) {
    echo "❌ Data tagihan tidak ditemukan!\n";
    exit;
}

echo "📋 DATA TAGIHAN:\n";
echo "Kontainer: {$tagihan->nomor_kontainer}\n";
echo "Periode: {$tagihan->periode}\n";
echo "Tarif: {$tagihan->tarif}\n";
echo "Masa: {$tagihan->masa}\n";
echo "Tanggal Awal: {$tagihan->tanggal_awal}\n";
echo "Tanggal Akhir: {$tagihan->tanggal_akhir}\n";
echo "DPP Saat Ini: Rp " . number_format((float)$tagihan->dpp, 0, ',', '.') . "\n";
echo "Vendor: {$tagihan->vendor}\n";
echo "Size: {$tagihan->size}\n\n";

// 2. Hitung jumlah hari
if ($tagihan->tanggal_awal && $tagihan->tanggal_akhir) {
    $startDate = Carbon::parse($tagihan->tanggal_awal);
    $endDate = Carbon::parse($tagihan->tanggal_akhir);
    $days = $startDate->diffInDays($endDate) + 1; // +1 karena termasuk hari pertama

    echo "📅 PERHITUNGAN HARI:\n";
    echo "Dari: {$startDate->format('d-m-Y')}\n";
    echo "Sampai: {$endDate->format('d-m-Y')}\n";
    echo "Jumlah Hari: {$days} hari\n\n";
} else {
    echo "❌ Tanggal tidak tersedia!\n";
    exit;
}

// 3. Cari master pricelist dengan vendor ZONA, ukuran_kontainer 40
echo "🔍 CARI PRICELIST VENDOR ZONA, UKURAN 40:\n";
$allPricelistZona = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
    ->where('ukuran_kontainer', '40')
    ->get();

if ($allPricelistZona->count() > 0) {
    foreach ($allPricelistZona as $pl) {
        echo "Tarif: {$pl->tarif} | Harga: Rp " . number_format((float)$pl->harga, 0, ',', '.') . " | Tanggal: {$pl->tanggal_harga_awal}\n";
    }
} else {
    echo "❌ Tidak ada pricelist untuk ZONA ukuran 40!\n";

    // Cek semua pricelist ZONA
    echo "\n🔍 SEMUA PRICELIST ZONA:\n";
    $allZona = MasterPricelistSewaKontainer::where('vendor', 'ZONA')->get();
    foreach ($allZona as $pl) {
        echo "Ukuran: {$pl->ukuran_kontainer} | Tarif: {$pl->tarif} | Harga: Rp " . number_format((float)$pl->harga, 0, ',', '.') . "\n";
    }
}

echo "\n";

// 4. Analisis perhitungan
echo "🧮 ANALISIS PERHITUNGAN:\n";
echo "Tanggal Awal: 05-12-2024\n";
echo "Tanggal Akhir: 24-12-2024\n";
echo "Jumlah Hari: {$days} hari\n";
echo "DPP Saat Ini: Rp " . number_format((float)$tagihan->dpp, 0, ',', '.') . "\n";
echo "Tarif per hari saat ini: Rp " . number_format((float)$tagihan->dpp / $days, 2, ',', '.') . "\n";

$expectedDPP = 42042 * $days;
echo "\nEkspektasi Anda:\n";
echo "Rp 42,042 × {$days} hari = Rp " . number_format($expectedDPP, 0, ',', '.') . "\n";
echo "Selisih: Rp " . number_format($expectedDPP - (float)$tagihan->dpp, 0, ',', '.') . "\n";

// 5. Cek apakah tanggal akhir salah?
echo "\n❓ KEMUNGKINAN MASALAH:\n";
echo "Masa yang tercatat: 5 Dec 2024 - 23 Dec 2024 (19 hari)\n";
echo "Tanggal database: 05-12-2024 - 24-12-2024 (20 hari)\n";
echo "Ada inkonsistensi antara field 'masa' dan tanggal database!\n";

// Hitung dengan 19 hari
$days19 = 19;
$dppWith19Days = (float)$tagihan->dpp / $days19;
echo "\nJika 19 hari:\n";
echo "Tarif per hari: Rp " . number_format($dppWith19Days, 2, ',', '.') . "\n";

echo "\n=== SELESAI ===\n";
