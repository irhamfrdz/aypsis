<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING CONTAINER RXTU5480180 ===\n\n";

try {
    $tagihans = \Illuminate\Support\Facades\DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'RXTU5480180')
        ->orderBy('periode')
        ->get();
    
    echo "Found " . $tagihans->count() . " records:\n\n";
    
    foreach ($tagihans as $tagihan) {
        echo "Periode: {$tagihan->periode}\n";
        echo "Vendor: {$tagihan->vendor}\n";
        echo "Tanggal Awal: {$tagihan->tanggal_awal}\n";
        echo "Tanggal Akhir: " . ($tagihan->tanggal_akhir ?: 'NULL') . "\n";
        echo "Masa: {$tagihan->masa}\n";
        echo "---\n";
    }
    
    if ($tagihans->count() > 0) {
        $firstRecord = $tagihans->first();
        echo "\nANALYSIS:\n";
        echo "Tanggal Awal: {$firstRecord->tanggal_awal}\n";
        echo "Tanggal Akhir: " . ($firstRecord->tanggal_akhir ?: 'NULL (ongoing)') . "\n";
        
        if ($firstRecord->tanggal_akhir) {
            echo "\n⚠️ ISSUE: Container has tanggal_akhir which limits periode generation\n";
            echo "SOLUTION: Remove or update tanggal_akhir to allow more periods\n";
        }
    }
    
} catch(\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n=== COMPLETE ===\n";

?>