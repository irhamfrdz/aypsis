<?php

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

/**
 * SCRIPT UNTUK MEMPERBAIKI DATA TAGIHAN KONTENER SEWA
 * 
 * Script ini akan:
 * 1. Mengubah vendor kontainer (jika belum sesuai)
 * 2. Mengambil harga DPP dari Master Pricelist berdasarkan Vendor & Ukuran
 * 3. Menghitung ulang PPN, PPH, dan Grand Total
 */

// -- KONFIGURASI --
// List kontainer yang ingin diperbaiki
$kontainerList = [
    'FSCU8295150',
    'TCLU5328217',
    // Tambahkan nomor kontainer lain di sini
];

// Vendor target
$targetVendor = 'ZONA'; 

// -- EXECUTION --
echo "=== MEMULAI PERBAIKAN DATA ===\n";
echo "Target Vendor: $targetVendor\n";
echo "Jumlah Kontainer di List: " . count($kontainerList) . "\n\n";

$totalUpdated = 0;

foreach ($kontainerList as $nomorKontainer) {
    echo "Memproses Kontainer: $nomorKontainer...\n";
    
    $items = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->get();
    
    if ($items->isEmpty()) {
        echo "  [SKIP] Tidak ditemukan data untuk kontainer ini.\n";
        continue;
    }
    
    echo "  Ditemukan " . $items->count() . " record.\n";
    
    foreach ($items as $item) {
        // 1. Update Vendor
        $item->vendor = $targetVendor;
        
        // 2. Ambil Harga dari Pricelist
        $pricelist = MasterPricelistSewaKontainer::where('vendor', $targetVendor)
                        ->where('ukuran_kontainer', $item->size)
                        ->first();
                        
        if ($pricelist) {
            $item->dpp = $pricelist->harga;
            $item->tarif = $pricelist->tarif;
            
            // 3. Hitung ulang Pajak & Total (Otomatis memanggil recalculateTaxes & calculateGrandTotal)
            $item->calculateGrandTotal();
            
            if ($item->save()) {
                $totalUpdated++;
            }
        } else {
            echo "  [WARNING] Pricelist tidak ditemukan untuk Vendor '$targetVendor' & Size '{$item->size}' pada ID: {$item->id}\n";
            // Tetap simpan vendornya saja
            $item->save();
        }
    }
    echo "  Selesai untuk $nomorKontainer.\n";
    echo "---------------------------------------------------\n";
}

echo "\n=== PROSES SELESAI ===\n";
echo "Total record yang berhasil diupdate: $totalUpdated\n";
