<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

echo "=== MENGECEK SEMUA DATA TAGIHAN ===" . PHP_EOL;
echo PHP_EOL;

// Ambil semua tagihan
$tagihans = DaftarTagihanKontainerSewa::all();

$errors = [];
$needUpdate = [];

foreach ($tagihans as $tagihan) {
    // Skip jika tidak ada vendor atau size
    if (!$tagihan->vendor || !$tagihan->size) {
        continue;
    }
    
    // Cari master pricelist
    $pricelist = MasterPricelistSewaKontainer::where('vendor', $tagihan->vendor)
        ->where('ukuran_kontainer', $tagihan->size)
        ->first();
    
    if (!$pricelist) {
        continue;
    }
    
    // Hitung jumlah hari
    $jumlahHari = 0;
    if ($tagihan->tanggal_awal && $tagihan->tanggal_akhir) {
        $jumlahHari = $tagihan->tanggal_awal->diffInDays($tagihan->tanggal_akhir) + 1;
    }
    
    // Hitung DPP yang seharusnya
    $dppSeharusnya = 0;
    $tarifType = strtolower($pricelist->tarif);
    
    if ($tarifType === 'bulanan') {
        $dppSeharusnya = $pricelist->harga;
    } else {
        $dppSeharusnya = $pricelist->harga * $jumlahHari;
    }
    
    // Cek apakah DPP berbeda
    $dppDatabase = floatval($tagihan->dpp);
    $diff = abs($dppDatabase - $dppSeharusnya);
    
    // Toleransi 1 rupiah untuk pembulatan
    if ($diff > 1) {
        $errors[] = [
            'id' => $tagihan->id,
            'kontainer' => $tagihan->nomor_kontainer,
            'periode' => $tagihan->periode,
            'vendor' => $tagihan->vendor,
            'size' => $tagihan->size,
            'jumlah_hari' => $jumlahHari,
            'tarif_type' => $pricelist->tarif,
            'tarif_nominal' => $pricelist->harga,
            'dpp_database' => $dppDatabase,
            'dpp_seharusnya' => $dppSeharusnya,
            'selisih' => $diff,
            'ppn_database' => floatval($tagihan->ppn),
            'grand_total_database' => floatval($tagihan->grand_total),
        ];
        
        // Prepare update data
        $ppnBaru = round($dppSeharusnya * 0.11, 2);
        $pphBaru = round($dppSeharusnya * 0.02, 2);
        $grandTotalBaru = $dppSeharusnya + $ppnBaru - $pphBaru;
        
        $needUpdate[] = [
            'id' => $tagihan->id,
            'kontainer' => $tagihan->nomor_kontainer,
            'periode' => $tagihan->periode,
            'dpp_lama' => $dppDatabase,
            'dpp_baru' => $dppSeharusnya,
            'ppn_lama' => floatval($tagihan->ppn),
            'ppn_baru' => $ppnBaru,
            'pph_lama' => floatval($tagihan->pph),
            'pph_baru' => $pphBaru,
            'grand_total_lama' => floatval($tagihan->grand_total),
            'grand_total_baru' => $grandTotalBaru,
            'tarif_type' => $pricelist->tarif,
        ];
    }
}

echo "=== HASIL PEMERIKSAAN ===" . PHP_EOL;
echo "Total Data: " . $tagihans->count() . PHP_EOL;
echo "Data Bermasalah: " . count($errors) . PHP_EOL;
echo PHP_EOL;

if (count($errors) > 0) {
    echo "=== DATA YANG BERMASALAH ===" . PHP_EOL;
    echo str_pad("ID", 6) . str_pad("Kontainer", 20) . str_pad("Per", 5) . str_pad("Vendor", 8) . str_pad("Tarif", 10) . str_pad("DPP Database", 18) . str_pad("DPP Seharusnya", 18) . str_pad("Selisih", 15) . PHP_EOL;
    echo str_repeat("-", 120) . PHP_EOL;
    
    foreach ($errors as $error) {
        echo str_pad($error['id'], 6) . 
             str_pad(substr($error['kontainer'], 0, 18), 20) . 
             str_pad($error['periode'], 5) . 
             str_pad($error['vendor'], 8) . 
             str_pad($error['tarif_type'], 10) . 
             str_pad(number_format($error['dpp_database'], 0, '.', ','), 18) . 
             str_pad(number_format($error['dpp_seharusnya'], 0, '.', ','), 18) . 
             str_pad(number_format($error['selisih'], 0, '.', ','), 15) . 
             PHP_EOL;
    }
    
    echo PHP_EOL;
    echo "=== RINGKASAN UPDATE ===" . PHP_EOL;
    
    $totalSelisihDpp = array_sum(array_column($errors, 'selisih'));
    $countBulanan = count(array_filter($errors, fn($e) => strtolower($e['tarif_type']) === 'bulanan'));
    $countHarian = count($errors) - $countBulanan;
    
    echo "Total Selisih DPP: Rp " . number_format($totalSelisihDpp, 2, '.', ',') . PHP_EOL;
    echo "Tarif Bulanan: " . $countBulanan . " data" . PHP_EOL;
    echo "Tarif Harian: " . $countHarian . " data" . PHP_EOL;
    echo PHP_EOL;
    
    // Simpan detail untuk update
    file_put_contents('data_need_update.json', json_encode($needUpdate, JSON_PRETTY_PRINT));
    echo "✅ Detail data disimpan ke: data_need_update.json" . PHP_EOL;
    
} else {
    echo "✅ Tidak ada data yang bermasalah!" . PHP_EOL;
}

echo PHP_EOL;
echo "=== SELESAI ===" . PHP_EOL;
