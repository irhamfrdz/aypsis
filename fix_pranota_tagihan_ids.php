<?php

/**
 * Script untuk memperbaiki format tagihan_kontainer_sewa_ids
 * yang ter-double encode (JSON string di dalam JSON string)
 * 
 * Usage: php fix_pranota_tagihan_ids.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PranotaTagihanKontainerSewa;

echo "========================================\n";
echo "FIX PRANOTA TAGIHAN IDS FORMAT\n";
echo "========================================\n";
echo "Script ini akan memperbaiki format tagihan_kontainer_sewa_ids\n";
echo "yang ter-double encode.\n\n";

try {
    // Ambil semua pranota
    $allPranota = PranotaTagihanKontainerSewa::all();
    
    echo "📊 Total pranota ditemukan: {$allPranota->count()}\n\n";
    
    if ($allPranota->count() === 0) {
        echo "❌ Tidak ada pranota yang perlu diperbaiki.\n";
        exit(0);
    }
    
    $fixedCount = 0;
    $skippedCount = 0;
    $errorCount = 0;
    
    DB::beginTransaction();
    
    foreach ($allPranota as $pranota) {
        echo "Memeriksa pranota #{$pranota->no_invoice}...\n";
        
        try {
            $rawValue = DB::table('pranota_tagihan_kontainer_sewa')
                ->where('id', $pranota->id)
                ->value('tagihan_kontainer_sewa_ids');
            
            // Cek apakah data kosong
            if (empty($rawValue) || $rawValue === 'null' || $rawValue === '[]') {
                echo "  ⚠️  Data kosong atau null, dilewati\n";
                $skippedCount++;
                continue;
            }
            
            // Decode pertama kali (dari database)
            $firstDecode = json_decode($rawValue, true);
            
            // Cek apakah hasil decode pertama adalah string (tanda double encode)
            if (is_string($firstDecode)) {
                echo "  🔧 Terdeteksi double encoding, memperbaiki...\n";
                
                // Decode lagi untuk mendapatkan array yang sebenarnya
                $actualArray = json_decode($firstDecode, true);
                
                if (is_array($actualArray) && !empty($actualArray)) {
                    // Update dengan array yang benar (model akan auto-encode sekali)
                    DB::table('pranota_tagihan_kontainer_sewa')
                        ->where('id', $pranota->id)
                        ->update([
                            'tagihan_kontainer_sewa_ids' => json_encode($actualArray),
                            'jumlah_tagihan' => count($actualArray)
                        ]);
                    
                    echo "  ✅ Diperbaiki: " . count($actualArray) . " tagihan\n";
                    $fixedCount++;
                } else {
                    echo "  ❌ Gagal decode data, format tidak valid\n";
                    $errorCount++;
                }
            } else if (is_array($firstDecode)) {
                // Data sudah benar (array), tidak perlu diperbaiki
                echo "  ✓  Format sudah benar: " . count($firstDecode) . " tagihan\n";
                
                // Update jumlah_tagihan jika tidak sesuai
                if ($pranota->jumlah_tagihan != count($firstDecode)) {
                    DB::table('pranota_tagihan_kontainer_sewa')
                        ->where('id', $pranota->id)
                        ->update(['jumlah_tagihan' => count($firstDecode)]);
                    echo "  🔧 Jumlah tagihan diupdate: " . count($firstDecode) . "\n";
                }
                
                $skippedCount++;
            } else {
                echo "  ⚠️  Format data tidak dikenali, dilewati\n";
                $skippedCount++;
            }
            
        } catch (\Exception $e) {
            echo "  ❌ Error: " . $e->getMessage() . "\n";
            $errorCount++;
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "========================================\n";
    echo "✅ PERBAIKAN SELESAI\n";
    echo "========================================\n";
    echo "Total pranota diperiksa: {$allPranota->count()}\n";
    echo "Diperbaiki: {$fixedCount}\n";
    echo "Sudah benar: {$skippedCount}\n";
    echo "Error: {$errorCount}\n";
    echo "\n";
    
    if ($fixedCount > 0) {
        echo "✅ {$fixedCount} pranota berhasil diperbaiki!\n";
        echo "Silakan buka halaman detail pranota untuk memverifikasi.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ TERJADI ERROR:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
