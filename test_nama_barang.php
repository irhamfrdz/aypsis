<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclKontainerPivot;
use Illuminate\Support\Facades\DB;

echo "=== Checking Nama Barang in Database ===\n";

// Check pivot records and their tanda_terima
$pivots = TandaTerimaLclKontainerPivot::with('tandaTerima')
    ->limit(10)
    ->get();

echo "Sample data from pivot -> tanda_terima:\n\n";

foreach ($pivots as $pivot) {
    if ($pivot->tandaTerima) {
        $tt = $pivot->tandaTerima;
        echo "Container: {$pivot->nomor_kontainer}\n";
        echo "- TT ID: {$tt->id}\n";
        echo "- TT Nomor: {$tt->nomor_tanda_terima}\n";
        echo "- nama_barang field: " . ($tt->nama_barang ?? 'NULL') . "\n";
        echo "- keterangan_barang field: " . ($tt->keterangan_barang ?? 'NULL') . "\n";
        
        // Check if there's items
        if ($tt->items && $tt->items->count() > 0) {
            $item = $tt->items->first();
            echo "- First item has nama_barang?: " . (isset($item->nama_barang) ? $item->nama_barang : 'NO FIELD') . "\n";
        }
        
        echo "\n";
    }
}

// Check raw database structure
echo "\n=== Checking Table Structure ===\n";
$columns = DB::select("DESCRIBE tanda_terimas_lcl");
echo "Columns in tanda_terimas_lcl:\n";
foreach ($columns as $col) {
    if (stripos($col->Field, 'barang') !== false || stripos($col->Field, 'nama') !== false) {
        echo "- {$col->Field} ({$col->Type})\n";
    }
}

// Check items table
echo "\nColumns in tanda_terima_lcl_items:\n";
$itemColumns = DB::select("DESCRIBE tanda_terima_lcl_items");
foreach ($itemColumns as $col) {
    if (stripos($col->Field, 'barang') !== false || stripos($col->Field, 'nama') !== false) {
        echo "- {$col->Field} ({$col->Type})\n";
    }
}
