<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get latest biaya kapal
$latest = DB::table('biaya_kapals')->latest('id')->first();

if (!$latest) {
    echo "No biaya kapal found.\n";
    exit;
}

echo "Latest Biaya Kapal ID: {$latest->id}\n";
echo "Nominal: Rp " . number_format($latest->nominal, 0, ',', '.') . "\n";
echo "Jenis Biaya: {$latest->jenis_biaya}\n\n";

// Get items
$items = DB::table('biaya_kapal_barang')
    ->where('biaya_kapal_id', $latest->id)
    ->get();

echo "Detail Items:\n";
echo str_repeat('=', 100) . "\n";

$totalCalculated = 0;
$groupedByKapal = [];

foreach ($items as $item) {
    $subtotalCalculated = $item->tarif * $item->jumlah;
    $totalCalculated += $subtotalCalculated;
    
    $key = $item->kapal . '|' . $item->voyage;
    if (!isset($groupedByKapal[$key])) {
        $groupedByKapal[$key] = [
            'kapal' => $item->kapal,
            'voyage' => $item->voyage,
            'subtotal' => 0,
            'items' => []
        ];
    }
    
    $groupedByKapal[$key]['subtotal'] += $subtotalCalculated;
    $groupedByKapal[$key]['items'][] = [
        'pricelist_buruh_id' => $item->pricelist_buruh_id,
        'jumlah' => $item->jumlah,
        'tarif' => $item->tarif,
        'subtotal_db' => $item->subtotal,
        'subtotal_calc' => $subtotalCalculated,
    ];
    
    echo "Kapal: {$item->kapal} | Voyage: {$item->voyage}\n";
    echo "  Pricelist ID: {$item->pricelist_buruh_id}\n";
    echo "  Jumlah: {$item->jumlah}\n";
    echo "  Tarif: Rp " . number_format($item->tarif, 0, ',', '.') . "\n";
    echo "  Subtotal (DB): Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
    echo "  Subtotal (Calc): Rp " . number_format($subtotalCalculated, 0, ',', '.') . "\n";
    echo "  Match: " . ($item->subtotal == $subtotalCalculated ? 'YES' : 'NO') . "\n";
    echo str_repeat('-', 100) . "\n";
}

echo "\nGrouped by Kapal + Voyage:\n";
echo str_repeat('=', 100) . "\n";

$no = 1;
foreach ($groupedByKapal as $group) {
    echo "{$no}. {$group['kapal']} - {$group['voyage']}\n";
    echo "   Total: Rp " . number_format($group['subtotal'], 0, ',', '.') . "\n";
    echo "   Items: " . count($group['items']) . "\n";
    foreach ($group['items'] as $item) {
        echo "     - {$item['jumlah']} x Rp " . number_format($item['tarif'], 0, ',', '.') . " = Rp " . number_format($item['subtotal_calc'], 0, ',', '.') . "\n";
    }
    $no++;
}

echo "\n" . str_repeat('=', 100) . "\n";
echo "Total Calculated: Rp " . number_format($totalCalculated, 0, ',', '.') . "\n";
echo "Total in DB (nominal): Rp " . number_format($latest->nominal, 0, ',', '.') . "\n";
echo "Match: " . ($totalCalculated == $latest->nominal ? 'YES' : 'NO') . "\n";

if ($totalCalculated != $latest->nominal) {
    echo "Difference: Rp " . number_format(abs($totalCalculated - $latest->nominal), 0, ',', '.') . "\n";
}
