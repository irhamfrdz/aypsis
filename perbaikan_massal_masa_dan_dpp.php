<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Events\EventServiceProvider;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PERBAIKAN MASSAL FORMAT MASA DAN DPP ===\n\n";

// Step 1: Perbaiki format masa untuk semua data
echo "Step 1: Memperbaiki format masa untuk semua data...\n";

$allTagihan = DaftarTagihanKontainerSewa::whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->get();

$masaFixed = 0;
foreach ($allTagihan as $tagihan) {
    $startDate = \Carbon\Carbon::parse($tagihan->tanggal_awal);
    $endDate = \Carbon\Carbon::parse($tagihan->tanggal_akhir);

    // Format masa baru: tanggal awal - tanggal akhir (tanpa dikurangi 1 hari)
    $masaBaru = $startDate->format('j M Y') . ' - ' . $endDate->format('j M Y');

    // Update jika berbeda
    if ($tagihan->masa !== $masaBaru) {
        $tagihan->masa = $masaBaru;
        $tagihan->save();
        $masaFixed++;
    }
}

echo "✅ Format masa diperbaiki untuk {$masaFixed} kontainer\n\n";

// Step 2: Perbaiki DPP untuk kontainer dengan tarif harian
echo "Step 2: Mencari dan memperbaiki DPP untuk kontainer tarif harian...\n";

// Ambil master pricelist
$pricelistHarian = MasterPricelistSewaKontainer::where('tarif', 'Harian')->get();
$pricelistMap = [];
foreach ($pricelistHarian as $price) {
    $key = $price->vendor . '_' . $price->ukuran_kontainer;
    $pricelistMap[$key] = $price->harga;
}

echo "Master pricelist harian loaded:\n";
foreach ($pricelistMap as $key => $harga) {
    echo "- {$key}: Rp " . number_format($harga, 0, ',', '.') . "\n";
}
echo "\n";

// Cari tagihan dengan tarif harian yang DPP-nya salah
$tagihanHarian = DaftarTagihanKontainerSewa::whereIn('tarif', ['Harian', 'harian', 'HARIAN'])
    ->whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->get();

echo "Found " . $tagihanHarian->count() . " tagihan dengan tarif harian\n\n";

$dppFixed = 0;
$errors = [];

foreach ($tagihanHarian as $tagihan) {
    try {
        // Cari master price
        $vendorSize = $tagihan->vendor . '_' . $tagihan->size;

        if (!isset($pricelistMap[$vendorSize])) {
            $errors[] = "Pricelist tidak ditemukan untuk {$vendorSize} (ID: {$tagihan->id})";
            continue;
        }

        $tarifHarian = $pricelistMap[$vendorSize];

        // Hitung hari dan DPP yang benar
        $startDate = \Carbon\Carbon::parse($tagihan->tanggal_awal);
        $endDate = \Carbon\Carbon::parse($tagihan->tanggal_akhir);
        $hari = $startDate->diffInDays($endDate) + 1;
        $dppBenar = $tarifHarian * $hari;

        // Cek apakah DPP saat ini salah (toleransi 1%)
        $selisih = abs($tagihan->dpp - $dppBenar);
        $persentaseSelisih = ($tagihan->dpp > 0) ? ($selisih / $tagihan->dpp) * 100 : 100;

        if ($persentaseSelisih > 1) {
            echo "Memperbaiki {$tagihan->nomor_kontainer} periode {$tagihan->periode}:\n";
            echo "  DPP Lama: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
            echo "  DPP Baru: Rp " . number_format($dppBenar, 0, ',', '.') . "\n";

            // Update DPP dan hitung ulang PPN, PPH, Grand Total
            $tagihan->dpp = $dppBenar;
            $tagihan->ppn = $dppBenar * 0.11; // PPN 11%
            $tagihan->pph = $dppBenar * 0.02; // PPH 2%
            $tagihan->grand_total = $dppBenar + $tagihan->ppn - $tagihan->pph;

            $tagihan->save();
            $dppFixed++;

            echo "  PPN Baru: Rp " . number_format($tagihan->ppn, 0, ',', '.') . "\n";
            echo "  PPH Baru: Rp " . number_format($tagihan->pph, 0, ',', '.') . "\n";
            echo "  Grand Total Baru: Rp " . number_format($tagihan->grand_total, 0, ',', '.') . "\n";
            echo "  ✅ Berhasil diperbaiki\n\n";
        }

    } catch (\Exception $e) {
        $errors[] = "Error untuk ID {$tagihan->id}: " . $e->getMessage();
    }
}

echo "=== HASIL PERBAIKAN ===\n";
echo "✅ Format masa diperbaiki: {$masaFixed} kontainer\n";
echo "✅ DPP diperbaiki: {$dppFixed} kontainer\n";

if (!empty($errors)) {
    echo "\n❌ ERRORS:\n";
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}

echo "\n=== PERBAIKAN SELESAI ===\n";
