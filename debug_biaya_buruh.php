<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get latest biaya buruh
$biayaKapal = \App\Models\BiayaKapal::where('jenis_biaya', 'KB024')
    ->with('barangDetails')
    ->latest()
    ->first();

if (!$biayaKapal) {
    echo "No biaya buruh found.\n";
    exit;
}

echo "=== DEBUG BIAYA BURUH ===\n";
echo "ID: {$biayaKapal->id}\n";
echo "Invoice: {$biayaKapal->nomor_invoice}\n";
echo "Nominal: Rp " . number_format($biayaKapal->nominal, 0, ',', '.') . "\n";
echo "\n";

echo "Total barangDetails records: {$biayaKapal->barangDetails->count()}\n";
echo "\n";

// Group by kapal|voyage
$grouped = $biayaKapal->barangDetails->groupBy(function($item) {
    return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-');
});

echo "Unique Kapal-Voyage combinations: {$grouped->count()}\n";
echo "\n";

foreach ($grouped as $key => $details) {
    list($kapal, $voyage) = explode('|', $key);
    $subtotal = $details->sum('subtotal');
    echo "  {$kapal} - {$voyage}\n";
    echo "    Items: {$details->count()}\n";
    echo "    Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
    echo "    Details:\n";
    foreach ($details as $detail) {
        $barang = $detail->pricelistBuruh ? $detail->pricelistBuruh->barang : 'NULL';
        echo "      - {$barang} x {$detail->jumlah} = Rp " . number_format($detail->subtotal, 0, ',', '.') . "\n";
    }
    echo "\n";
}
