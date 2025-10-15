<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

echo "=== MENCARI KONTAINER TEXU7210230 PERIODE 6 ===\n";

$container = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'TEXU7210230')
    ->where('periode', 6)
    ->first();

if ($container) {
    echo "Container ditemukan:\n";
    echo "- Nomor Kontainer: {$container->nomor_kontainer}\n";
    echo "- Vendor: {$container->vendor}\n";
    echo "- Size: {$container->size}\n";
    echo "- Periode: {$container->periode}\n";
    echo "- Masa: {$container->masa}\n";
    echo "- Tarif: {$container->tarif}\n";
    echo "- DPP: Rp " . number_format($container->dpp, 0, ',', '.') . "\n";
    echo "- Tanggal Awal: {$container->tanggal_awal}\n";
    echo "- Tanggal Akhir: {$container->tanggal_akhir}\n";

    // Hitung jumlah hari aktual
    $startDate = \Carbon\Carbon::parse($container->tanggal_awal);
    $endDate = \Carbon\Carbon::parse($container->tanggal_akhir);
    $actualDays = $startDate->diffInDays($endDate) + 1;
    echo "- Jumlah hari aktual: $actualDays\n";

    echo "\n=== CEK MASTER PRICELIST ===\n";
    $pricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $container->size)
        ->where('vendor', $container->vendor)
        ->first();

    if ($pricelist) {
        echo "Master Pricelist ditemukan:\n";
        echo "- Vendor: {$pricelist->vendor}\n";
        echo "- Ukuran: {$pricelist->ukuran_kontainer}\n";
        echo "- Tarif Type: {$pricelist->tarif}\n";
        echo "- Harga: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";

        echo "\n=== ANALISIS PERHITUNGAN ===\n";
        if (strtolower($pricelist->tarif) === 'bulanan') {
            echo "Tarif BULANAN: DPP = {$pricelist->harga} (tidak dikali hari)\n";
            echo "Expected DPP: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
        } else {
            echo "Tarif HARIAN: DPP = {$pricelist->harga} × $actualDays hari\n";
            $expectedDpp = $pricelist->harga * $actualDays;
            echo "Expected DPP: Rp " . number_format($expectedDpp, 0, ',', '.') . "\n";
        }

        echo "Actual DPP: Rp " . number_format($container->dpp, 0, ',', '.') . "\n";

        if ($container->dpp != $pricelist->harga && strtolower($pricelist->tarif) === 'bulanan') {
            echo "\n❌ MASALAH DITEMUKAN: DPP tidak sesuai untuk tarif bulanan!\n";
            echo "Selisih: Rp " . number_format($container->dpp - $pricelist->harga, 0, ',', '.') . "\n";
            echo "Kemungkinan: DPP dihitung sebagai tarif harian × hari, padahal seharusnya tarif bulanan tetap\n";
        } elseif ($container->dpp != ($pricelist->harga * $actualDays) && strtolower($pricelist->tarif) === 'harian') {
            echo "\n❌ MASALAH DITEMUKAN: DPP tidak sesuai untuk tarif harian!\n";
        } else {
            echo "\n✅ DPP sudah benar\n";
        }
    } else {
        echo "Master Pricelist TIDAK ditemukan!\n";
    }
} else {
    echo "Container TEXU7210230 dengan periode 6 tidak ditemukan!\n";

    // Cari semua record dengan nomor kontainer tersebut
    $allRecords = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'TEXU7210230')->get();
    echo "\nDitemukan " . $allRecords->count() . " record dengan nomor kontainer TEXU7210230:\n";
    foreach ($allRecords as $record) {
        echo "- Periode: {$record->periode}, DPP: Rp " . number_format($record->dpp, 0, ',', '.') . "\n";
    }
}

// Juga cek beberapa container lain untuk pola yang sama
echo "\n=== CEK POLA SERUPA ===\n";
$similarCases = DaftarTagihanKontainerSewa::where('dpp', '>', 5000000)
    ->where('periode', 6)
    ->take(5)
    ->get();

if ($similarCases->count() > 0) {
    echo "Ditemukan " . $similarCases->count() . " container lain dengan DPP tinggi pada periode 6:\n";
    foreach ($similarCases as $case) {
        echo "- {$case->nomor_kontainer}: Rp " . number_format($case->dpp, 0, ',', '.') . " ({$case->vendor}, {$case->size}ft)\n";
    }
}
