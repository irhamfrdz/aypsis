<?php

/**
 * Script untuk menghapus data BL dengan nomor voyage SA19BJ25
 * 
 * Usage: php delete_bls_voyage_SA19BJ25.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    DB::beginTransaction();

    echo "===========================================\n";
    echo "Script Hapus BL dengan Voyage SA19BJ25\n";
    echo "===========================================\n\n";

    // Cari data BL dengan voyage SA19BJ25
    $bls = Bl::where('no_voyage', 'SA19BJ25')->get();
    
    $count = $bls->count();

    if ($count === 0) {
        echo "Tidak ada data BL dengan voyage SA19BJ25\n";
        DB::rollBack();
        exit(0);
    }

    echo "Ditemukan {$count} data BL dengan voyage SA19BJ25:\n\n";
    
    // Tampilkan detail data yang akan dihapus
    foreach ($bls as $index => $bl) {
        echo ($index + 1) . ". ID: {$bl->id}, No BL: {$bl->no_bl}, Nama Kapal: {$bl->nama_kapal}, Voyage: {$bl->no_voyage}\n";
    }

    echo "\n";
    echo "Apakah Anda yakin ingin menghapus {$count} data BL ini? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);

    if ($confirmation !== 'yes' && $confirmation !== 'y') {
        echo "\nPenghapusan dibatalkan.\n";
        DB::rollBack();
        exit(0);
    }

    // Hapus data
    echo "\nMenghapus data...\n";
    
    $deleted = Bl::where('no_voyage', 'SA19BJ25')->delete();

    // Log aktivitas
    Log::info('BL data deleted via script', [
        'voyage' => 'SA19BJ25',
        'count' => $deleted,
        'executed_at' => now(),
        'deleted_ids' => $bls->pluck('id')->toArray(),
    ]);

    DB::commit();

    echo "\n===========================================\n";
    echo "✓ Berhasil menghapus {$deleted} data BL\n";
    echo "===========================================\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n===========================================\n";
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "===========================================\n\n";
    
    Log::error('Error deleting BL data via script', [
        'voyage' => 'SA19BJ25',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    exit(1);
}
