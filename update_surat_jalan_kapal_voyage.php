<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalanBongkaran;
use App\Models\Bl;

echo "=== Update Surat Jalan yang nama_kapal dan no_voyage NULL ===\n\n";

// Ambil semua surat jalan yang nama_kapal atau no_voyage NULL
$suratJalansWithoutKapalVoyage = SuratJalanBongkaran::where(function($q) {
    $q->whereNull('nama_kapal')
      ->orWhereNull('no_voyage');
})->get();

echo "Total Surat Jalan dengan nama_kapal atau no_voyage NULL: " . $suratJalansWithoutKapalVoyage->count() . "\n\n";

if ($suratJalansWithoutKapalVoyage->isEmpty()) {
    echo "Semua surat jalan sudah memiliki nama_kapal dan no_voyage!\n";
    exit(0);
}

$updated = 0;
$failed = 0;

foreach ($suratJalansWithoutKapalVoyage as $sj) {
    echo "Surat Jalan ID: {$sj->id}, Nomor: {$sj->nomor_surat_jalan}\n";
    echo "  - Kapal sekarang: " . ($sj->nama_kapal ?? 'NULL') . "\n";
    echo "  - Voyage sekarang: " . ($sj->no_voyage ?? 'NULL') . "\n";
    
    // Coba dapatkan dari BL
    if ($sj->bl_id) {
        $bl = Bl::find($sj->bl_id);
        if ($bl) {
            $sj->nama_kapal = $bl->nama_kapal;
            $sj->no_voyage = $bl->no_voyage;
            $sj->save();
            
            echo "  - Diupdate dari BL ID {$bl->id}\n";
            echo "  - Kapal baru: {$bl->nama_kapal}\n";
            echo "  - Voyage baru: {$bl->no_voyage}\n";
            $updated++;
        } else {
            echo "  - BL ID {$sj->bl_id} tidak ditemukan\n";
            $failed++;
        }
    } else {
        // Coba cari BL berdasarkan no_bl atau no_kontainer
        $bl = null;
        
        if ($sj->no_bl) {
            $bl = Bl::where('nomor_bl', $sj->no_bl)->first();
        }
        
        if (!$bl && $sj->no_kontainer) {
            $bl = Bl::where('nomor_kontainer', $sj->no_kontainer)->first();
        }
        
        if ($bl) {
            $sj->nama_kapal = $bl->nama_kapal;
            $sj->no_voyage = $bl->no_voyage;
            $sj->bl_id = $bl->id;
            $sj->save();
            
            echo "  - Ditemukan dan diupdate dari BL ID {$bl->id}\n";
            echo "  - Kapal baru: {$bl->nama_kapal}\n";
            echo "  - Voyage baru: {$bl->no_voyage}\n";
            $updated++;
        } else {
            echo "  - Tidak bisa menemukan BL terkait\n";
            $failed++;
        }
    }
    
    echo "\n";
}

echo "\n=== Ringkasan ===\n";
echo "Total diupdate: {$updated}\n";
echo "Total gagal: {$failed}\n";

// Tampilkan data setelah update
echo "\n=== Verifikasi setelah update ===\n";
$stillNull = SuratJalanBongkaran::where(function($q) {
    $q->whereNull('nama_kapal')
      ->orWhereNull('no_voyage');
})->count();

echo "Surat Jalan yang masih NULL: {$stillNull}\n";
