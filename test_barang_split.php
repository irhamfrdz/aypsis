<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclKontainerPivot;
use Illuminate\Support\Facades\DB;

// Test 1: Check pivot records
echo "=== Test 1: Checking Pivot Records ===\n";
$count = TandaTerimaLclKontainerPivot::count();
echo "Total pivot records: $count\n";

if ($count > 0) {
    $samplePivots = TandaTerimaLclKontainerPivot::with('tandaTerima')
        ->limit(3)
        ->get();
    
    echo "\nSample pivot records:\n";
    foreach ($samplePivots as $pivot) {
        echo "- Container: {$pivot->nomor_kontainer}, Tanda Terima: " . 
             ($pivot->tandaTerima ? $pivot->tandaTerima->nomor_tanda_terima : 'NULL') . "\n";
    }
}

// Test 2: Check if tanda terima has items via pivot
echo "\n\n=== Test 2: Checking Tanda Terima with Items via Pivot ===\n";
$pivot = TandaTerimaLclKontainerPivot::with(['tandaTerima.items'])
    ->first();

if ($pivot && $pivot->tandaTerima) {
    $tt = $pivot->tandaTerima;
    echo "Container: " . $pivot->nomor_kontainer . "\n";
    echo "Tanda Terima: " . $tt->nomor_tanda_terima . "\n";
    echo "Items count: " . $tt->items->count() . "\n";
    echo "Nama Barang: " . ($tt->nama_barang ?? 'N/A') . "\n";
    
    if ($tt->items->count() > 0) {
        $firstItem = $tt->items->first();
        echo "\nFirst Item Details:\n";
        echo "- ID: " . $firstItem->id . "\n";
        echo "- Panjang: " . $firstItem->panjang . "\n";
        echo "- Lebar: " . $firstItem->lebar . "\n";
        echo "- Tinggi: " . $firstItem->tinggi . "\n";
        echo "- M3: " . $firstItem->meter_kubik . "\n";
    }
} else {
    echo "No pivot found or no tanda terima\n";
}

// Test 3: Simulate the controller method
if ($pivot) {
    echo "\n\n=== Test 3: Simulating Controller Method ===\n";
    $containers = [$pivot->nomor_kontainer];
    echo "Testing with containers: " . json_encode($containers) . "\n\n";

    $pivotRecords = TandaTerimaLclKontainerPivot::with(['tandaTerima.items'])
        ->whereIn('nomor_kontainer', $containers)
        ->get();

    echo "Found " . $pivotRecords->count() . " pivot records\n\n";

    $barangData = [];
    $seenItems = [];

    foreach ($pivotRecords as $p) {
        if (!$p->tandaTerima) {
            echo "Pivot without tanda_terima: pivot_id={$p->id}\n";
            continue;
        }
        
        $tandaTerima = $p->tandaTerima;
        echo "Processing Pivot: Container={$p->nomor_kontainer}, TT={$tandaTerima->nomor_tanda_terima}\n";
        echo "- Has items: " . ($tandaTerima->items ? 'Yes' : 'No') . "\n";
        echo "- Items count: " . ($tandaTerima->items ? $tandaTerima->items->count() : 0) . "\n";
        
        if ($tandaTerima->items && $tandaTerima->items->count() > 0) {
            foreach ($tandaTerima->items as $item) {
                $itemKey = $item->id;
                
                if (!isset($seenItems[$itemKey])) {
                    $seenItems[$itemKey] = true;
                    
                    $barangData[] = [
                        'id' => $item->id,
                        'nama_barang' => $tandaTerima->nama_barang ?? 'N/A',
                        'satuan' => $tandaTerima->keterangan_barang ?? '',
                        'panjang' => $item->panjang,
                        'lebar' => $item->lebar,
                        'tinggi' => $item->tinggi,
                        'jumlah' => $tandaTerima->kuantitas ?? 1,
                        'meter_kubik' => $item->meter_kubik,
                        'tonase' => $item->tonase,
                        'display_label' => ($tandaTerima->nama_barang ?? 'N/A') . 
                                         ($tandaTerima->kuantitas ? ' (' . $tandaTerima->kuantitas . ' pcs)' : '') .
                                         ($item->panjang && $item->lebar && $item->tinggi ? 
                                             ' - ' . $item->panjang . 'x' . $item->lebar . 'x' . $item->tinggi . 'm' : '') .
                                         ($item->meter_kubik ? ' - ' . number_format($item->meter_kubik, 3) . 'm³' : '')
                    ];
                }
            }
        }
    }

    echo "\nTotal barang collected: " . count($barangData) . "\n";
    if (count($barangData) > 0) {
        echo "\nFirst barang:\n";
        print_r($barangData[0]);
    }
} else {
    echo "\n\nSkipping Test 3 - No pivot found\n";
}
