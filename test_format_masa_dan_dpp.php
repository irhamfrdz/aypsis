<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Events\EventServiceProvider;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST FORMAT MASA DAN PERBAIKAN DPP ===\n\n";

// Ambil satu contoh data bermasalah
$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'MSCU7085120')
    ->where('periode', 6)
    ->first();

if ($tagihan) {
    echo "=== SEBELUM PERBAIKAN ===\n";
    echo "Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "Periode: {$tagihan->periode}\n";
    echo "Masa: {$tagihan->masa}\n";
    echo "DPP: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
    echo "Tanggal Awal: {$tagihan->tanggal_awal}\n";
    echo "Tanggal Akhir: {$tagihan->tanggal_akhir}\n\n";
    
    // Test format baru masa
    $startDate = \Carbon\Carbon::parse($tagihan->tanggal_awal);
    $endDate = \Carbon\Carbon::parse($tagihan->tanggal_akhir);
    $masaBaru = $startDate->format('j M Y') . ' - ' . $endDate->format('j M Y');
    
    echo "=== FORMAT MASA BARU ===\n";
    echo "Masa Lama: {$tagihan->masa}\n";
    echo "Masa Baru: {$masaBaru}\n\n";
    
    // Cek tarif yang benar
    $pricelist = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
        ->where('ukuran_kontainer', '40')
        ->where('tarif', 'Harian')
        ->first();
    
    if ($pricelist) {
        $hari = $startDate->diffInDays($endDate) + 1;
        $dppBenar = $pricelist->harga * $hari;
        
        echo "=== PERHITUNGAN DPP YANG BENAR ===\n";
        echo "Tarif Harian: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
        echo "Jumlah Hari: {$hari}\n";
        echo "DPP Seharusnya: Rp " . number_format($dppBenar, 0, ',', '.') . "\n";
        echo "DPP Saat Ini: Rp " . number_format($tagihan->dpp, 0, ',', '.') . "\n";
        echo "Selisih: Rp " . number_format($dppBenar - $tagihan->dpp, 0, ',', '.') . "\n\n";
    }
} else {
    echo "❌ Data MSCU7085120 periode 6 tidak ditemukan\n";
}

echo "=== SELESAI ===\n";