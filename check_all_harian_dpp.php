<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== CHECKING ALL HARIAN TARIF DATA ===\n\n";

// Ambil semua data dengan tarif Harian
$tagihanList = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('tarif', 'Harian')
    ->get();

echo "Total data dengan tarif Harian: " . $tagihanList->count() . "\n\n";

if ($tagihanList->count() == 0) {
    echo "Tidak ada data dengan tarif Harian.\n";
    exit;
}

// Get pricelist untuk perhitungan
$pricelists = DB::table('master_pricelist_sewa_kontainers')
    ->where('tarif', 'Harian')
    ->get()
    ->keyBy(function($item) {
        return $item->vendor . '_' . $item->ukuran_kontainer;
    });

$salahCount = 0;
$benarCount = 0;

echo "Mengecek perhitungan DPP untuk setiap record...\n\n";

foreach ($tagihanList as $tagihan) {
    $start = Carbon::parse($tagihan->tanggal_awal);
    $end = Carbon::parse($tagihan->tanggal_akhir);
    $jumlahHari = $start->diffInDays($end) + 1;
    
    $key = $tagihan->vendor . '_' . $tagihan->size;
    $pricelist = $pricelists->get($key);
    
    if (!$pricelist) {
        echo "⚠️  ID {$tagihan->id}: Pricelist tidak ditemukan untuk {$tagihan->vendor} size {$tagihan->size}\n";
        continue;
    }
    
    $dppSeharusnya = $pricelist->harga * $jumlahHari;
    $dppDatabase = $tagihan->dpp;
    $selisih = abs($dppSeharusnya - $dppDatabase);
    
    if ($selisih > 1) { // Toleransi pembulatan 1
        $salahCount++;
        echo "❌ ID {$tagihan->id}: {$tagihan->nomor_kontainer}\n";
        echo "   Masa: {$tagihan->masa}\n";
        echo "   Tanggal: {$tagihan->tanggal_awal} s/d {$tagihan->tanggal_akhir}\n";
        echo "   Jumlah Hari: {$jumlahHari}\n";
        echo "   Harga Harian: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
        echo "   DPP Seharusnya: Rp " . number_format($dppSeharusnya, 0, ',', '.') . "\n";
        echo "   DPP di Database: Rp " . number_format($dppDatabase, 0, ',', '.') . "\n";
        echo "   Selisih: Rp " . number_format($selisih, 0, ',', '.') . "\n";
        
        // Hitung berapa hari yang digunakan
        if ($pricelist->harga > 0) {
            $hariTerpakai = round($dppDatabase / $pricelist->harga, 2);
            echo "   Hari yang digunakan: {$hariTerpakai}\n";
        }
        echo "\n";
    } else {
        $benarCount++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total data: " . $tagihanList->count() . "\n";
echo "Data SALAH: {$salahCount}\n";
echo "Data BENAR: {$benarCount}\n";

if ($salahCount > 0) {
    echo "\n⚠️  Ada {$salahCount} data yang perlu diperbaiki!\n";
    echo "Jalankan script fix_dpp_harian.php untuk memperbaikinya.\n";
}
