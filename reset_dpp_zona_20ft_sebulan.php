<?php

/**
 * Script untuk reset DPP kontainer vendor zona 20ft yang sudah sebulan
 * Mengubah DPP menjadi Rp 675,676
 * 
 * Usage: php reset_dpp_zona_20ft_sebulan.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== SCRIPT RESET DPP ZONA 20FT SEBULAN ===\n";
echo "Target: Kontainer vendor zona 20ft yang sudah sebulan (>= 30 hari)\n";
echo "DPP Baru: Rp 675,676\n\n";

// Konfirmasi
echo "Apakah Anda yakin ingin melanjutkan? (yes/no): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'yes') {
    echo "Script dibatalkan.\n";
    exit;
}

echo "\n";

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
    
    // Filter yang sudah >= 30 hari
    $kontainerSebulan = $allKontainer->filter(function($item) {
        $days = Carbon::parse($item->tanggal_awal)->diffInDays(Carbon::parse($item->tanggal_akhir)) + 1;
        return $days >= 30;
    });
    
    $totalRecords = $kontainerSebulan->count();
    
    echo "Ditemukan $totalRecords kontainer yang memenuhi kriteria:\n";
    echo "- Vendor: Zona\n";
    echo "- Ukuran: 20ft\n";
    echo "- Durasi: >= 30 hari (dihitung dari tanggal_awal ke tanggal_akhir)\n\n";
    
    if ($totalRecords === 0) {
        echo "Tidak ada data yang perlu diupdate.\n";
        DB::rollBack();
        exit;
    }
    
    // Tampilkan preview data yang akan diupdate
    echo "Preview data yang akan diupdate (10 data pertama):\n";
    echo str_repeat("-", 130) . "\n";
    printf("%-10s %-20s %-15s %-10s %-10s %-15s %-15s\n", 
        "ID", "No Kontainer", "Vendor", "Ukuran", "Hari", "DPP Lama", "DPP Baru");
    echo str_repeat("-", 130) . "\n";
    
    $previewData = $kontainerSebulan->take(10);
    foreach ($previewData as $tagihan) {
        $days = Carbon::parse($tagihan->tanggal_awal)->diffInDays(Carbon::parse($tagihan->tanggal_akhir)) + 1;
        printf("%-10s %-20s %-15s %-10s %-10s %-15s %-15s\n",
            $tagihan->id,
            $tagihan->nomor_kontainer,
            substr($tagihan->vendor ?? 'N/A', 0, 15),
            $tagihan->size . 'ft',
            $days . ' hari',
            'Rp ' . number_format($tagihan->dpp, 0, ',', '.'),
            'Rp ' . number_format($newDpp, 0, ',', '.')
        );
    }
    echo str_repeat("-", 130) . "\n\n";
    
    // Konfirmasi lagi sebelum update
    echo "Lanjutkan update $totalRecords data? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $finalConfirmation = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($finalConfirmation) !== 'yes') {
        echo "Update dibatalkan.\n";
        DB::rollBack();
        exit;
    }
    
    echo "\nMemulai update...\n";
    
    // Update data
    $updatedCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($kontainerSebulan as $tagihan) {
        try {
            $oldDpp = $tagihan->dpp;
            $days = Carbon::parse($tagihan->tanggal_awal)->diffInDays(Carbon::parse($tagihan->tanggal_akhir)) + 1;
            
            // Update DPP
            $tagihan->dpp = $newDpp;
            
            // Recalculate PPN (11% dari DPP)
            $tagihan->ppn = round($newDpp * 0.11);
            
            // Recalculate Grand Total (DPP + PPN + Adjustment)
            $tagihan->grand_total = $newDpp + $tagihan->ppn + ($tagihan->adjustment ?? 0);
            
            $tagihan->save();
            
            $updatedCount++;
            
            echo sprintf("✓ Updated ID %s - %s (%d hari): Rp %s → Rp %s\n",
                $tagihan->id,
                $tagihan->nomor_kontainer,
                $days,
                number_format($oldDpp, 0, ',', '.'),
                number_format($newDpp, 0, ',', '.')
            );
            
        } catch (\Exception $e) {
            $errorCount++;
            $errors[] = "Error updating ID {$tagihan->id}: " . $e->getMessage();
            echo "✗ Error updating ID {$tagihan->id}: {$e->getMessage()}\n";
        }
    }
    
    DB::commit();
    
    echo "\n";
    echo str_repeat("=", 80) . "\n";
    echo "UPDATE SELESAI\n";
    echo str_repeat("=", 80) . "\n";
    echo "Total diupdate: $updatedCount dari $totalRecords\n";
    echo "Error: $errorCount\n";
    
    if ($errorCount > 0) {
        echo "\nDetail Error:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
    echo "\nDPP baru: Rp " . number_format($newDpp, 0, ',', '.') . "\n";
    echo "PPN otomatis dihitung: 11% dari DPP\n";
    echo "Grand Total otomatis diupdate: DPP + PPN + Adjustment\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Semua perubahan dibatalkan (rollback).\n";
}

echo "\nScript selesai.\n";
