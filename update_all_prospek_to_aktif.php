<?php

/**
 * Script untuk mengubah semua status prospek menjadi 'aktif'
 * 
 * Cara menjalankan:
 * php update_all_prospek_to_aktif.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Prospek;
use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "  UPDATE STATUS PROSPEK KE AKTIF\n";
echo "==============================================\n\n";

try {
    // Hitung total prospek
    $totalProspek = Prospek::count();
    echo "Total prospek di database: {$totalProspek}\n";
    
    // Hitung prospek yang bukan aktif
    $prospekNonAktif = Prospek::where('status', '!=', 'aktif')->count();
    echo "Prospek yang bukan aktif: {$prospekNonAktif}\n\n";
    
    if ($prospekNonAktif == 0) {
        echo "✓ Semua prospek sudah berstatus 'aktif'.\n";
        echo "Tidak ada yang perlu diupdate.\n\n";
        exit(0);
    }
    
    // Konfirmasi
    echo "⚠️  WARNING: Script ini akan mengubah {$prospekNonAktif} prospek menjadi status 'aktif'\n";
    echo "Apakah Anda yakin ingin melanjutkan? (ketik 'yes' untuk melanjutkan): ";
    
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirmation) !== 'yes') {
        echo "\n❌ Update dibatalkan oleh user.\n\n";
        exit(0);
    }
    
    echo "\n🔄 Memulai update...\n\n";
    
    // Update dengan transaction
    DB::beginTransaction();
    
    try {
        // Get prospek yang akan diupdate untuk log
        $prospeksToUpdate = Prospek::where('status', '!=', 'aktif')
            ->select('id', 'no_surat_jalan', 'status', 'nama_supir')
            ->get();
        
        // Simpan log sebelum update
        $logFile = storage_path('logs/prospek_status_update_' . date('Y-m-d_H-i-s') . '.log');
        $logContent = "UPDATE PROSPEK STATUS - " . date('Y-m-d H:i:s') . "\n";
        $logContent .= "========================================\n\n";
        $logContent .= "Prospek yang diupdate:\n\n";
        
        foreach ($prospeksToUpdate as $prospek) {
            $logContent .= sprintf(
                "ID: %d | No. Surat Jalan: %s | Supir: %s | Status Lama: %s\n",
                $prospek->id,
                $prospek->no_surat_jalan ?? '-',
                $prospek->nama_supir ?? '-',
                $prospek->status
            );
        }
        
        file_put_contents($logFile, $logContent);
        
        // Update semua prospek ke status aktif
        $updated = Prospek::where('status', '!=', 'aktif')
            ->update(['status' => 'aktif']);
        
        DB::commit();
        
        echo "✓ Berhasil mengupdate {$updated} prospek ke status 'aktif'\n";
        echo "✓ Log disimpan di: {$logFile}\n\n";
        
        // Verifikasi hasil
        $totalAktif = Prospek::where('status', 'aktif')->count();
        $totalNonAktif = Prospek::where('status', '!=', 'aktif')->count();
        
        echo "==============================================\n";
        echo "  HASIL UPDATE\n";
        echo "==============================================\n";
        echo "Total prospek aktif: {$totalAktif}\n";
        echo "Total prospek non-aktif: {$totalNonAktif}\n";
        echo "==============================================\n\n";
        
        echo "✓ Update selesai dengan sukses!\n\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    exit(1);
}
