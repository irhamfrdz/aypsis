<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=============================================================\n";
    echo "UPDATE SIZE KONTAINER PADA HALAMAN OB\n";
    echo "Berdasarkan data dari stock_kontainers dan kontainers\n";
    echo "=============================================================\n\n";

    // Function to normalize container number (remove spaces and punctuation)
    function normalizeContainerNumber($number) {
        if (!$number) return '';
        // Remove all spaces, dashes, dots, and convert to uppercase
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $number));
    }

    // Step 1: Build mapping from stock_kontainers
    echo "📦 Mengambil data dari stock_kontainers...\n";
    $stockKontainers = DB::table('stock_kontainers')
        ->whereNotNull('nomor_seri_gabungan')
        ->whereNotNull('ukuran')
        ->select('nomor_seri_gabungan', 'ukuran')
        ->get();

    $sizeMapping = [];
    foreach ($stockKontainers as $stock) {
        $normalized = normalizeContainerNumber($stock->nomor_seri_gabungan);
        if ($normalized) {
            $sizeMapping[$normalized] = $stock->ukuran;
        }
    }
    echo "   ✓ Ditemukan " . count($sizeMapping) . " kontainer dari stock_kontainers\n\n";

    // Step 2: Build mapping from kontainers
    echo "📦 Mengambil data dari kontainers...\n";
    $kontainers = DB::table('kontainers')
        ->whereNotNull('nomor_seri_gabungan')
        ->whereNotNull('ukuran')
        ->select('nomor_seri_gabungan', 'ukuran')
        ->get();

    foreach ($kontainers as $kontainer) {
        $normalized = normalizeContainerNumber($kontainer->nomor_seri_gabungan);
        if ($normalized && !isset($sizeMapping[$normalized])) {
            $sizeMapping[$normalized] = $kontainer->ukuran;
        }
    }
    echo "   ✓ Total mapping: " . count($sizeMapping) . " kontainer\n\n";

    // Step 3: Update size_kontainer in bls table
    echo "🔄 Memproses table bls...\n";
    $blsToUpdate = DB::table('bls')
        ->whereNotNull('nomor_kontainer')
        ->select('id', 'nomor_kontainer', 'size_kontainer')
        ->get();

    $blsUpdated = 0;
    $blsNotFound = 0;
    $blsAlreadyCorrect = 0;

    foreach ($blsToUpdate as $bl) {
        $normalized = normalizeContainerNumber($bl->nomor_kontainer);
        
        if ($normalized && isset($sizeMapping[$normalized])) {
            $newSize = $sizeMapping[$normalized];
            
            // Check if size needs update
            if ($bl->size_kontainer != $newSize) {
                DB::table('bls')
                    ->where('id', $bl->id)
                    ->update(['size_kontainer' => $newSize]);
                
                echo "   ✓ BL ID {$bl->id}: {$bl->nomor_kontainer} → Size: {$newSize}\n";
                $blsUpdated++;
            } else {
                $blsAlreadyCorrect++;
            }
        } else {
            if ($normalized) {
                $blsNotFound++;
            }
        }
    }

    echo "\n📊 Hasil update table bls:\n";
    echo "   - Diupdate: {$blsUpdated}\n";
    echo "   - Sudah benar: {$blsAlreadyCorrect}\n";
    echo "   - Tidak ditemukan mapping: {$blsNotFound}\n\n";

    // Step 4: Update size_kontainer in naik_kapal table
    echo "🔄 Memproses table naik_kapal...\n";
    $naikKapalsToUpdate = DB::table('naik_kapal')
        ->whereNotNull('nomor_kontainer')
        ->select('id', 'nomor_kontainer', 'size_kontainer')
        ->get();

    $naikKapalsUpdated = 0;
    $naikKapalsNotFound = 0;
    $naikKapalsAlreadyCorrect = 0;

    foreach ($naikKapalsToUpdate as $naikKapal) {
        $normalized = normalizeContainerNumber($naikKapal->nomor_kontainer);
        
        if ($normalized && isset($sizeMapping[$normalized])) {
            $newSize = $sizeMapping[$normalized];
            
            // Check if size needs update
            if ($naikKapal->size_kontainer != $newSize) {
                DB::table('naik_kapal')
                    ->where('id', $naikKapal->id)
                    ->update(['size_kontainer' => $newSize]);
                
                echo "   ✓ Naik Kapal ID {$naikKapal->id}: {$naikKapal->nomor_kontainer} → Size: {$newSize}\n";
                $naikKapalsUpdated++;
            } else {
                $naikKapalsAlreadyCorrect++;
            }
        } else {
            if ($normalized) {
                $naikKapalsNotFound++;
            }
        }
    }

    echo "\n📊 Hasil update table naik_kapal:\n";
    echo "   - Diupdate: {$naikKapalsUpdated}\n";
    echo "   - Sudah benar: {$naikKapalsAlreadyCorrect}\n";
    echo "   - Tidak ditemukan mapping: {$naikKapalsNotFound}\n\n";

    // Final Summary
    echo "=============================================================\n";
    echo "RINGKASAN UPDATE\n";
    echo "=============================================================\n";
    echo "Total mapping kontainer: " . count($sizeMapping) . "\n";
    echo "BLS - Diupdate: {$blsUpdated} | Sudah benar: {$blsAlreadyCorrect} | Tidak ditemukan: {$blsNotFound}\n";
    echo "NAIK_KAPAL - Diupdate: {$naikKapalsUpdated} | Sudah benar: {$naikKapalsAlreadyCorrect} | Tidak ditemukan: {$naikKapalsNotFound}\n";
    echo "=============================================================\n";
    echo "\n✅ Script selesai dijalankan!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
