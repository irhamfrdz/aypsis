<?php

/**
 * Script untuk reset DPP kontainer vendor zona 20ft yang sudah >= 1 bulan
 * Menghitung dari selisih bulan antara tanggal_awal dan tanggal_akhir
 * Mengubah DPP menjadi Rp 675,676
 * 
 * Usage: php reset_dpp_zona_20ft_v2.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== SCRIPT RESET DPP ZONA 20FT (BERDASARKAN SELISIH BULAN) ===\n";
echo "Target: Kontainer vendor zona 20ft yang sudah >= 1 bulan\n";
echo "Perhitungan: Selisih bulan dari tanggal_awal ke tanggal_akhir\n";
echo "Contoh: 20 Feb - 19 Mar = 1 bulan (meskipun hanya 27-28 hari)\n";
echo "DPP Baru: Rp 675,676\n\n";

try {
    DB::beginTransaction();
    
    $newDpp = 675676; // Rp 675,676
    
    // Query untuk mencari kontainer vendor zona 20ft
    $allKontainer = DaftarTagihanKontainerSewa::query()
        ->where(function($q) {
            $q->where('vendor', 'LIKE', '%zona%')
              ->orWhere('vendor', 'LIKE', '%ZONA%');
        })
        ->where('size', '20')
        ->whereNotNull('tanggal_awal')
        ->whereNotNull('tanggal_akhir')
        ->get();
    
    // Filter yang sudah >= 1 bulan (menggunakan diffInMonths)
    $kontainerSebulan = $allKontainer->filter(function($item) {
        $tanggalAwal = Carbon::parse($item->tanggal_awal);
        $tanggalAkhir = Carbon::parse($item->tanggal_akhir);
        
        // diffInMonths menghitung selisih bulan penuh
        // 20 Feb - 19 Mar = 0 bulan (belum genap 1 bulan)
        // 20 Feb - 20 Mar = 1 bulan
        // Tapi kita mau 20 Feb - 19 Mar juga dianggap 1 bulan
        // Jadi kita pakai logika: jika selisih hari >= 28, dianggap 1 bulan
        $diffDays = $tanggalAwal->diffInDays($tanggalAkhir) + 1;
        
        // Atau bisa pakai: jika tanggal akhir >= tanggal awal + 1 bulan - 3 hari
        // Untuk handle bulan dengan 28-31 hari
        return $diffDays >= 28;
    });
    
    $totalRecords = $kontainerSebulan->count();
    
    echo "Ditemukan $totalRecords kontainer yang memenuhi kriteria:\n";
    echo "- Vendor: Zona\n";
    echo "- Ukuran: 20ft\n";
    echo "- Durasi: >= 28 hari (dianggap 1 bulan)\n\n";
    
    if ($totalRecords === 0) {
        echo "Tidak ada data yang perlu diupdate.\n";
        DB::rollBack();
        exit;
    }
    
    // Tampilkan preview data yang akan diupdate
    echo "Preview data yang akan diupdate (10 data pertama):\n";
    echo str_repeat("-", 150) . "\n";
    printf("%-10s %-20s %-15s %-10s %-10s %-30s %-15s %-15s\n", 
        "ID", "No Kontainer", "Vendor", "Ukuran", "Hari", "Masa", "DPP Lama", "DPP Baru");
    echo str_repeat("-", 150) . "\n";
    
    $previewData = $kontainerSebulan->take(10);
    foreach ($previewData as $tagihan) {
        $days = Carbon::parse($tagihan->tanggal_awal)->diffInDays(Carbon::parse($tagihan->tanggal_akhir)) + 1;
        printf("%-10s %-20s %-15s %-10s %-10d %-30s %-15s %-15s\n",
            $tagihan->id,
            substr($tagihan->nomor_kontainer, 0, 20),
            substr($tagihan->vendor, 0, 15),
            $tagihan->size,
            $days,
            substr($tagihan->masa, 0, 30),
            'Rp ' . number_format($tagihan->dpp, 0, ',', '.'),
            'Rp ' . number_format($newDpp, 0, ',', '.')
        );
    }
    
    echo str_repeat("-", 150) . "\n\n";
    
    // Konfirmasi kedua
    echo "Lanjutkan update $totalRecords data? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirmation) !== 'yes') {
        echo "Update dibatalkan.\n";
        DB::rollBack();
        exit;
    }
    
    echo "\nMemulai update...\n\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($kontainerSebulan as $tagihan) {
        try {
            $days = Carbon::parse($tagihan->tanggal_awal)->diffInDays(Carbon::parse($tagihan->tanggal_akhir)) + 1;
            
            $oldDpp = $tagihan->dpp;
            $newPpn = round($newDpp * 0.11); // PPN 11%
            
            // Update DPP, PPN, dan Grand Total
            $tagihan->dpp = $newDpp;
            $tagihan->ppn = $newPpn;
            
            // Grand Total = DPP + PPN + Adjustment (jika ada)
            $adjustment = $tagihan->grand_total - ($oldDpp + $tagihan->ppn);
            $tagihan->grand_total = $newDpp + $newPpn + $adjustment;
            
            $tagihan->save();
            
            $successCount++;
            echo "✓ Updated ID {$tagihan->id} - {$tagihan->nomor_kontainer} ({$days} hari): Rp " . 
                 number_format($oldDpp, 0, ',', '.') . " → Rp " . number_format($newDpp, 0, ',', '.') . "\n";
                 
        } catch (\Exception $e) {
            $errorCount++;
            echo "✗ Error updating ID {$tagihan->id}: {$e->getMessage()}\n";
        }
    }
    
    DB::commit();
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "UPDATE SELESAI!\n";
    echo "Berhasil: $successCount data\n";
    echo "Gagal: $errorCount data\n";
    echo str_repeat("=", 80) . "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\nERROR: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
    exit(1);
}
