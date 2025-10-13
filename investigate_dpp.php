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

// 3. Cek master pricelist
echo "🏷️ MASTER PRICELIST:\n";
$masterPricelistHarian = MasterPricelistSewaKontainer::where('vendor', $tagihan->vendor)
    ->where('zona', 'ZONA')
    ->where('size', $tagihan->size)
    ->where('tarif', 'Harian')
    ->first();

$masterPricelistBulanan = MasterPricelistSewaKontainer::where('vendor', $tagihan->vendor)
    ->where('zona', 'ZONA')
    ->where('size', $tagihan->size)
    ->where('tarif', 'Bulanan')
    ->first();

if ($masterPricelistHarian) {
    echo "Harian: Rp " . number_format($masterPricelistHarian->harga_sewa, 0, ',', '.') . "\n";
} else {
    echo "❌ Master pricelist harian tidak ditemukan!\n";
}

if ($masterPricelistBulanan) {
    echo "Bulanan: Rp " . number_format($masterPricelistBulanan->harga_sewa, 0, ',', '.') . "\n";
} else {
    echo "❌ Master pricelist bulanan tidak ditemukan!\n";
}

echo "\n";

// 4. Perhitungan yang seharusnya
echo "🧮 PERHITUNGAN YANG SEHARUSNYA:\n";

if ($tagihan->tarif === 'Harian' && $masterPricelistHarian) {
    $correctDPP = $masterPricelistHarian->harga_sewa * $days;
    echo "Tarif Harian: Rp " . number_format($masterPricelistHarian->harga_sewa, 0, ',', '.') . " × {$days} hari\n";
    echo "DPP Seharusnya: Rp " . number_format($correctDPP, 0, ',', '.') . "\n";
    echo "DPP Saat Ini: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
    echo "Selisih: Rp " . number_format($correctDPP - $tagihan->dpp, 0, ',', '.') . "\n";

    if ($correctDPP != $tagihan->dpp) {
        echo "❌ DPP TIDAK SESUAI!\n";
    } else {
        echo "✅ DPP SUDAH BENAR!\n";
    }
} elseif ($tagihan->tarif === 'Bulanan' && $masterPricelistBulanan) {
    $correctDPP = $masterPricelistBulanan->harga_sewa * $tagihan->periode;
    echo "Tarif Bulanan: Rp " . number_format($masterPricelistBulanan->harga_sewa, 0, ',', '.') . " × {$tagihan->periode} periode\n";
    echo "DPP Seharusnya: Rp " . number_format($correctDPP, 0, ',', '.') . "\n";
    echo "DPP Saat Ini: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
    echo "Selisih: Rp " . number_format($correctDPP - $tagihan->dpp, 0, ',', '.') . "\n";

    if ($correctDPP != $tagihan->dpp) {
        echo "❌ DPP TIDAK SESUAI!\n";
    } else {
        echo "✅ DPP SUDAH BENAR!\n";
    }
} else {
    echo "❌ Tidak dapat menghitung: Master pricelist atau tarif tidak sesuai!\n";
}

echo "\n";

// 5. Analisis tambahan
echo "🔍 ANALISIS TAMBAHAN:\n";
echo "Ekspektasi Anda: ~Rp 840,000 (sekitar 20 hari × Rp 42,042)\n";
echo "Hasil Aktual: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";

if ($days && $masterPricelistHarian) {
    $expectedFromUser = 42042 * $days;
    echo "Perhitungan dengan Rp 42,042: Rp " . number_format($expectedFromUser, 0, ',', '.') . "\n";

    // Cari tahu berapa tarif yang digunakan untuk menghasilkan DPP saat ini
    $actualRate = $tagihan->dpp / $days;
    echo "Tarif yang digunakan saat ini: Rp " . number_format($actualRate, 0, ',', '.') . " per hari\n";
}

echo "\n=== SELESAI ===\n";
