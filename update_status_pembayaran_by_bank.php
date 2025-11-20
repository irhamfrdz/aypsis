<?php

/**
 * Script untuk update status pranota menjadi "sudah_dibayar"
 * jika tagihan kontainer sewa sudah memiliki nomor bank
 * 
 * Usage: php update_status_pembayaran_by_bank.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Update Status Pranota by Nomor Bank\n";
echo "========================================\n\n";

try {
    // Start transaction
    DB::beginTransaction();
    
    // Find all tagihan that have nomor_bank but status_pranota is not 'sudah_dibayar'
    $tagihans = DaftarTagihanKontainerSewa::where(function($query) {
            $query->whereNull('status_pranota')
                  ->orWhere('status_pranota', '!=', 'sudah_dibayar');
        })
        ->whereNotNull('nomor_bank')
        ->where('nomor_bank', '!=', '')
        ->get();
    
    $totalFound = $tagihans->count();
    
    echo "Ditemukan {$totalFound} tagihan dengan nomor bank tapi status pranota bukan 'sudah_dibayar'\n\n";
    
    if ($totalFound === 0) {
        echo "✓ Tidak ada tagihan yang perlu diupdate.\n";
        DB::rollBack();
        exit(0);
    }
    
    echo "Detail tagihan yang akan diupdate:\n";
    echo str_repeat("-", 130) . "\n";
    printf("%-5s %-20s %-20s %-30s %-25s\n", "No", "Nomor Kontainer", "Vendor", "Nomor Bank", "Status Pranota Saat Ini");
    echo str_repeat("-", 130) . "\n";
    
    foreach ($tagihans as $index => $tagihan) {
        $vendor = $tagihan->vendor ? $tagihan->vendor->nama_perusahaan : 'N/A';
        $kontainer = $tagihan->kontainer ? $tagihan->kontainer->nomor_kontainer : 'N/A';
        $currentStatus = $tagihan->status_pranota ?? 'NULL';
        
        printf(
            "%-5s %-20s %-20s %-30s %-25s\n",
            $index + 1,
            substr($kontainer, 0, 20),
            substr($vendor, 0, 20),
            substr($tagihan->nomor_bank ?? 'N/A', 0, 30),
            substr($currentStatus, 0, 25)
        );
    }
    
    echo str_repeat("-", 130) . "\n\n";
    
    // Ask for confirmation
    echo "Apakah Anda yakin ingin mengupdate {$totalFound} tagihan menjadi status_pranota 'sudah_dibayar'? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($line) !== 'yes') {
        echo "\n❌ Update dibatalkan oleh user.\n";
        DB::rollBack();
        exit(0);
    }
    
    echo "\n";
    echo "Memproses update...\n\n";
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($tagihans as $index => $tagihan) {
        try {
            $oldStatus = $tagihan->status_pranota ?? 'NULL';
            
            // Update status pranota
            $tagihan->status_pranota = 'sudah_dibayar';
            $tagihan->save();
            
            $successCount++;
            
            $vendor = $tagihan->vendor ? $tagihan->vendor->nama_perusahaan : 'N/A';
            $kontainer = $tagihan->kontainer ? $tagihan->kontainer->nomor_kontainer : 'N/A';
            
            echo sprintf(
                "[%d/%d] ✓ Updated: %s - %s (Bank: %s) | %s → %s\n",
                $index + 1,
                $totalFound,
                $kontainer,
                $vendor,
                $tagihan->nomor_bank,
                $oldStatus,
                $tagihan->status_pranota
            );
            
        } catch (\Exception $e) {
            $errorCount++;
            $errors[] = [
                'id' => $tagihan->id,
                'kontainer' => $tagihan->kontainer ? $tagihan->kontainer->nomor_kontainer : 'N/A',
                'error' => $e->getMessage()
            ];
            
            echo sprintf(
                "[%d/%d] ✗ Error: ID %d - %s\n",
                $index + 1,
                $totalFound,
                $tagihan->id,
                $e->getMessage()
            );
        }
    }
    
    // Commit transaction
    DB::commit();
    
    echo "\n";
    echo str_repeat("=", 80) . "\n";
    echo "SUMMARY\n";
    echo str_repeat("=", 80) . "\n";
    echo "Total ditemukan     : {$totalFound}\n";
    echo "Berhasil diupdate   : {$successCount}\n";
    echo "Gagal               : {$errorCount}\n";
    echo str_repeat("=", 80) . "\n";
    
    if ($errorCount > 0) {
        echo "\nERRORS:\n";
        foreach ($errors as $error) {
            echo "  - ID {$error['id']} ({$error['kontainer']}): {$error['error']}\n";
        }
    }
    
    if ($successCount > 0) {
        echo "\n✓ Update status pranota selesai!\n";
        echo "  {$successCount} tagihan telah diupdate status_pranota menjadi 'sudah_dibayar'\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✓ Script selesai dijalankan.\n";
