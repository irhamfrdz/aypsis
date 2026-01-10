<?php

/**
 * Script untuk update jumlah_rit pada data pranota uang rit yang sudah ada
 * Jalankan dengan: php update_jumlah_rit_existing_data.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PranotaUangRitSupirDetail;
use App\Models\PranotaUangRit;
use Illuminate\Support\Facades\DB;

echo "Starting update jumlah_rit for existing data...\n\n";

DB::beginTransaction();

try {
    // Ambil semua detail supir yang jumlah_rit-nya 0
    $details = PranotaUangRitSupirDetail::where('jumlah_rit', 0)->get();
    
    echo "Found " . $details->count() . " records with jumlah_rit = 0\n\n";
    
    foreach ($details as $detail) {
        // Ambil pranota induknya
        $pranota = PranotaUangRit::where('no_pranota', $detail->no_pranota)->first();
        
        if (!$pranota) {
            echo "❌ Pranota {$detail->no_pranota} not found\n";
            continue;
        }
        
        // Hitung jumlah rit dari no_surat_jalan dan supir_nama
        $suratJalanArray = explode(', ', $pranota->no_surat_jalan);
        $supirArray = explode(', ', $pranota->supir_nama);
        
        // Hitung berapa kali supir ini muncul
        $jumlahRit = 0;
        foreach ($supirArray as $supir) {
            if (trim($supir) === trim($detail->supir_nama)) {
                $jumlahRit++;
            }
        }
        
        // Update jumlah_rit
        $detail->jumlah_rit = $jumlahRit;
        $detail->save();
        
        echo "✓ Updated {$detail->no_pranota} - {$detail->supir_nama}: {$jumlahRit} rit\n";
    }
    
    DB::commit();
    echo "\n✅ Update completed successfully!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
