<?php

/**
 * Script untuk update status pembayaran menjadi "sudah_dibayar"
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
echo "Update Status Pembayaran by Nomor Bank\n";
echo "========================================\n\n";

try {
    // Start transaction
    DB::beginTransaction();
    
    // Find all tagihan that have nomor_bank but status is still 'belum_dibayar'
    $tagihans = DaftarTagihanKontainerSewa::where('status_pembayaran', 'belum_dibayar')
        ->whereNotNull('nomor_bank')
        ->where('nomor_bank', '!=', '')
        ->get();
    
    $totalFound = $tagihans->count();
    
    echo "Ditemukan {$totalFound} tagihan dengan nomor bank tapi status masih 'belum_dibayar'\n\n";
    
    if ($totalFound === 0) {
        echo "✓ Tidak ada tagihan yang perlu diupdate.\n";
        DB::rollBack();
        exit(0);
    }
    
    echo "Detail tagihan yang akan diupdate:\n";
    echo str_repeat("-", 120) . "\n";
    printf("%-5s %-20s %-20s %-30s %-20s\n", "No", "Nomor Kontainer", "Vendor", "Nomor Bank", "Status Saat Ini");
    echo str_repeat("-", 120) . "\n";
    
    foreach ($tagihans as $index => $tagihan) {
        $vendor = $tagihan->vendor ? $tagihan->vendor->nama_perusahaan : 'N/A';
        $kontainer = $tagihan->kontainer ? $tagihan->kontainer->nomor_kontainer : 'N/A';
        
        printf(
            "%-5s %-20s %-20s %-30s %-20s\n",
            $index + 1,
            substr($kontainer, 0, 20),
            substr($vendor, 0, 20),
            substr($tagihan->nomor_bank ?? 'N/A', 0, 30),
            $tagihan->status_pembayaran
        );
    }
    
    echo str_repeat("-", 120) . "\n\n";
    
    // Ask for confirmation
    echo "Apakah Anda yakin ingin mengupdate {$totalFound} tagihan menjadi status 'sudah_dibayar'? (yes/no): ";
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
            $oldStatus = $tagihan->status_pembayaran;
            
            // Update status pembayaran
            $tagihan->status_pembayaran = 'sudah_dibayar';
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
                $tagihan->status_pembayaran
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
        echo "\n✓ Update status pembayaran selesai!\n";
        echo "  {$successCount} tagihan telah diupdate menjadi 'sudah_dibayar'\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✓ Script selesai dijalankan.\n";
