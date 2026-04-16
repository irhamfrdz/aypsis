<?php

/**
 * Script untuk memperbaiki data History Kontainer yang terlewat.
 * Script ini akan memeriksa setiap kontainer dan stock kontainer, 
 * lalu membandingkan lokasi gudang saat ini dengan rekaman terakhir di history.
 * Jika tidak cocok, script akan membuat rekaman history baru.
 * 
 * Cara jalankan: php fix_history_kontainer.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\HistoryKontainer;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Memulai Proses Perbaikan History Kontainer ---\n";

$fixed = 0;
$totalMismatches = 0;

// Fungsi untuk cek dan perbaiki mismatch
function fixMismatches($query, $type) {
    global $fixed, $totalMismatches;
    
    $query->whereNotNull('gudangs_id')->chunk(50, function($items) use ($type, &$fixed, &$totalMismatches) {
        foreach ($items as $item) {
            $lastHistory = HistoryKontainer::where('nomor_kontainer', $item->nomor_seri_gabungan)
                ->orderBy('id', 'desc')
                ->first();
            
            // Jika tidak ada history ATAU gudang di history terakhir berbeda dengan gudang saat ini
            if (!$lastHistory || $lastHistory->gudang_id != $item->gudangs_id) {
                $totalMismatches++;
                
                try {
                    DB::beginTransaction();
                    
                    HistoryKontainer::create([
                        'nomor_kontainer' => $item->nomor_seri_gabungan,
                        'tipe_kontainer' => ($type == 'stock' ? 'stock' : 'kontainer'),
                        'jenis_kegiatan' => 'Masuk (Penyesuaian)',
                        'tanggal_kegiatan' => now(),
                        'asal_gudang_id' => $lastHistory ? $lastHistory->gudang_id : null,
                        'gudang_id' => $item->gudangs_id,
                        'keterangan' => 'Penyesuaian data otomatis (Fixing missing movement history)',
                        'created_by' => 1, // System Default
                    ]);

                    DB::commit();
                    $fixed++;
                    echo "Fixed [{$type}]: {$item->nomor_seri_gabungan}\n";
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo "Error fixing {$item->nomor_seri_gabungan}: " . $e->getMessage() . "\n";
                }
            }
        }
    });
}

echo "Memeriksa Master Kontainer...\n";
fixMismatches(Kontainer::query(), 'kontainer');

echo "Memeriksa Stock Kontainer...\n";
fixMismatches(StockKontainer::query(), 'stock');

echo "\n--- Perbaikan Selesai ---\n";
echo "Total Mismatch Ditemukan: {$totalMismatches}\n";
echo "Total Berhasil Diperbaiki: {$fixed}\n";
echo "----------------------------------------\n";
