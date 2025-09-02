<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranPranotaKontainer;

echo "=== CHECKING PAYMENT CALCULATION ERROR ===\n\n";

$payment = PembayaranPranotaKontainer::with('items')->find(2);

if ($payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Nomor Pembayaran: {$payment->nomor_pembayaran}\n\n";

    echo "DATABASE VALUES:\n";
    echo "- total_pembayaran: " . ($payment->total_pembayaran ?? 'NULL') . "\n";
    echo "- total_tagihan_penyesuaian: " . ($payment->total_tagihan_penyesuaian ?? 'NULL') . "\n";
    echo "- total_tagihan_setelah_penyesuaian: " . ($payment->total_tagihan_setelah_penyesuaian ?? 'NULL') . "\n\n";

    echo "PRANOTA ITEMS:\n";
    $itemsTotal = 0;
    foreach ($payment->items as $index => $item) {
        $amount = $item->amount ?? 0;
        $itemsTotal += $amount;
        echo "- Item " . ($index + 1) . ": Rp " . number_format($amount, 0, ',', '.') . "\n";
    }
    echo "- Total Items: Rp " . number_format($itemsTotal, 0, ',', '.') . "\n\n";

    echo "CALCULATION CHECK:\n";
    $correctTotal = $itemsTotal + ($payment->total_tagihan_penyesuaian ?? 0);
    echo "- Items Total: " . $itemsTotal . "\n";
    echo "- Penyesuaian: " . ($payment->total_tagihan_penyesuaian ?? 0) . "\n";
    echo "- Should be: " . $correctTotal . "\n";
    echo "- Currently stored: " . ($payment->total_tagihan_setelah_penyesuaian ?? $payment->total_pembayaran) . "\n\n";

    if ($payment->total_pembayaran != $itemsTotal) {
        echo "❌ PROBLEM FOUND: total_pembayaran ({$payment->total_pembayaran}) != items total ({$itemsTotal})\n";

        // Fix the total_pembayaran to match items
        echo "\n=== FIXING THE ISSUE ===\n";
        $payment->total_pembayaran = $itemsTotal;
        $payment->total_tagihan_setelah_penyesuaian = $itemsTotal + ($payment->total_tagihan_penyesuaian ?? 0);
        $payment->save();

        echo "✅ Fixed total_pembayaran: {$itemsTotal}\n";
        echo "✅ Fixed total_tagihan_setelah_penyesuaian: {$payment->total_tagihan_setelah_penyesuaian}\n";
    } else {
        echo "✅ total_pembayaran is correct\n";
    }

} else {
    echo "❌ Payment not found\n";
}

?>
