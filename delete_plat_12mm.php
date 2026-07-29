<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StockAmprahan;

$targetName = "plat 12mm";

echo "Mencari data dengan nama barang EXACT MATCH: '{$targetName}'...\n";

$stocks = StockAmprahan::where('nama_barang', 'LIKE', $targetName)->get();

// Memastikan EXACT match (case-insensitive)
$filteredStocks = $stocks->filter(function($stock) use ($targetName) {
    return strtolower(trim($stock->nama_barang)) === strtolower(trim($targetName));
});

if ($filteredStocks->isEmpty()) {
    echo "Data tidak ditemukan dengan nama persis '{$targetName}'.\n";
    exit;
}

echo "Ditemukan " . $filteredStocks->count() . " data stock.\n";

$deletedUsages = 0;
$deletedStocks = 0;

DB::beginTransaction();
try {
    foreach ($filteredStocks as $stock) {
        // Hapus child data (pemakaian)
        $usagesCount = $stock->usages()->count();
        if ($usagesCount > 0) {
            $stock->usages()->delete();
            $deletedUsages += $usagesCount;
        }
        
        // Hapus parent data (stock)
        $stock->delete();
        $deletedStocks++;
    }
    DB::commit();
    echo "\nBERHASIL DIHAPUS!\n";
    echo "- $deletedUsages riwayat pemakaian (usages) terhapus.\n";
    echo "- $deletedStocks data stock terhapus.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "\nTERJADI KESALAHAN, rollback dilakukan. Error: " . $e->getMessage() . "\n";
}
