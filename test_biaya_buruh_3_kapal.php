<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BiayaKapal;
use App\Models\BiayaKapalBarang;
use App\Models\PricelistBuruh;
use Illuminate\Support\Facades\DB;

echo "=== TEST: Create Biaya Buruh with 3 Kapal ===\n\n";

// Get pricelist buruh items
$pricelist = PricelistBuruh::all();
echo "Available Pricelist Buruh:\n";
foreach ($pricelist as $p) {
    echo "  ID: {$p->id} - {$p->barang} - Rp " . number_format($p->tarif, 0, ',', '.') . "\n";
}
echo "\n";

// Define 3 different kapal with different voyages
$kapalSections = [
    [
        'kapal' => 'KM. ALKEN PRINCESS',
        'voyage' => 'AP01JB26',
        'barang' => [
            ['barang_id' => 32, 'jumlah' => 50], // Kontainer 20 Full
            ['barang_id' => 34, 'jumlah' => 10], // Kontainer 40 Full
        ]
    ],
    [
        'kapal' => 'KM Sentosa 18',
        'voyage' => 'ST01JP26',
        'barang' => [
            ['barang_id' => 32, 'jumlah' => 30], // Kontainer 20 Full
            ['barang_id' => 33, 'jumlah' => 5],  // Kontainer 20 Empty
        ]
    ],
    [
        'kapal' => 'KM. SRIWIJAYA RAYA',
        'voyage' => 'SR01JB26',
        'barang' => [
            ['barang_id' => 32, 'jumlah' => 40], // Kontainer 20 Full
            ['barang_id' => 35, 'jumlah' => 8],  // Kontainer 40 Empty
        ]
    ],
];

echo "Creating Biaya Buruh with 3 Kapal sections...\n";

DB::beginTransaction();
try {
    // Calculate total nominal
    $totalNominal = 0;
    foreach ($kapalSections as $section) {
        foreach ($section['barang'] as $item) {
            $barang = PricelistBuruh::find($item['barang_id']);
            if ($barang) {
                $totalNominal += $barang->tarif * $item['jumlah'];
            }
        }
    }

    // Create BiayaKapal
    $biayaKapal = BiayaKapal::create([
        'tanggal' => now()->format('Y-m-d'),
        'jenis_biaya' => 'KB024', // Biaya Buruh
        'penerima' => 'TEST PENERIMA',
        'nominal' => $totalNominal,
        'nomor_invoice' => 'T-' . now()->format('mdHis'),
        'created_by' => 1,
    ]);

    echo "Created BiayaKapal ID: {$biayaKapal->id}\n";
    echo "Nominal: Rp " . number_format($totalNominal, 0, ',', '.') . "\n\n";

    // Create BiayaKapalBarang for each section
    foreach ($kapalSections as $sectionIndex => $section) {
        echo "Processing Section " . ($sectionIndex + 1) . ": {$section['kapal']} - {$section['voyage']}\n";
        
        foreach ($section['barang'] as $item) {
            $barang = PricelistBuruh::find($item['barang_id']);
            if ($barang) {
                $subtotal = $barang->tarif * $item['jumlah'];
                
                BiayaKapalBarang::create([
                    'biaya_kapal_id' => $biayaKapal->id,
                    'pricelist_buruh_id' => $barang->id,
                    'kapal' => $section['kapal'],
                    'voyage' => $section['voyage'],
                    'jumlah' => $item['jumlah'],
                    'tarif' => $barang->tarif,
                    'subtotal' => $subtotal,
                    'total_nominal' => 0,
                    'dp' => 0,
                    'sisa_pembayaran' => 0,
                ]);
                
                echo "  - {$barang->barang} x {$item['jumlah']} = Rp " . number_format($subtotal, 0, ',', '.') . "\n";
            }
        }
    }

    DB::commit();
    echo "\n✅ BiayaKapal created successfully!\n\n";

    // Now verify the data
    echo "=== VERIFICATION ===\n";
    $biayaKapal->load('barangDetails');
    
    $grouped = $biayaKapal->barangDetails->groupBy(function($item) {
        return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-');
    });

    echo "Total barangDetails records: {$biayaKapal->barangDetails->count()}\n";
    echo "Unique Kapal-Voyage combinations: {$grouped->count()}\n\n";

    foreach ($grouped as $key => $details) {
        list($kapal, $voyage) = explode('|', $key);
        $subtotal = $details->sum('subtotal');
        echo "  {$kapal} - {$voyage}\n";
        echo "    Items: {$details->count()}, Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
    }

    echo "\n🎯 Expected: 3 unique Kapal-Voyage combinations\n";
    echo "📊 Actual: {$grouped->count()} unique Kapal-Voyage combinations\n";

    if ($grouped->count() === 3) {
        echo "\n✅ TEST PASSED! All 3 kapal saved correctly!\n";
    } else {
        echo "\n❌ TEST FAILED! Only {$grouped->count()} kapal saved.\n";
    }

    echo "\nBiaya Kapal ID for print test: {$biayaKapal->id}\n";
    echo "URL: /biaya-kapal/{$biayaKapal->id}/print\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
