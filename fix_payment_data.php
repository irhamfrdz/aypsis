<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranPranotaKontainer;

echo "=== FIXING PAYMENT DATA ===\n\n";

$payment = PembayaranPranotaKontainer::with('items')->find(2);

if ($payment) {
    echo "Current Payment Data:\n";
    echo "- Nomor: {$payment->nomor_pembayaran}\n";
    echo "- Total Pembayaran: " . number_format($payment->total_pembayaran, 0, ',', '.') . "\n";
    echo "- Penyesuaian: " . number_format($payment->total_tagihan_penyesuaian ?? 0, 0, ',', '.') . "\n";
    echo "- Total Setelah Penyesuaian: " . number_format($payment->total_tagihan_setelah_penyesuaian ?? 0, 0, ',', '.') . "\n\n";

    // Calculate correct values
    $itemsTotal = $payment->items->sum('amount');
    echo "Items total from pranota: " . number_format($itemsTotal, 0, ',', '.') . "\n\n";

    // Reset to reasonable values
    $correctAdjustment = 0; // No adjustment for clean demo
    $correctTotal = $itemsTotal + $correctAdjustment;

    echo "Setting correct values:\n";
    echo "- total_pembayaran: " . number_format($itemsTotal, 0, ',', '.') . "\n";
    echo "- total_tagihan_penyesuaian: " . number_format($correctAdjustment, 0, ',', '.') . "\n";
    echo "- total_tagihan_setelah_penyesuaian: " . number_format($correctTotal, 0, ',', '.') . "\n\n";

    // Update the payment
    $payment->update([
        'total_pembayaran' => $itemsTotal,
        'total_tagihan_penyesuaian' => $correctAdjustment,
        'total_tagihan_setelah_penyesuaian' => $correctTotal
    ]);

    echo "✅ Payment data has been corrected!\n\n";

    // Verify the fix
    $payment->refresh();
    echo "Verification - Updated values:\n";
    echo "- Total Pembayaran: Rp " . number_format($payment->total_pembayaran, 0, ',', '.') . "\n";
    echo "- Penyesuaian: Rp " . number_format($payment->total_tagihan_penyesuaian ?? 0, 0, ',', '.') . "\n";
    echo "- Total Setelah Penyesuaian: Rp " . number_format($payment->total_tagihan_setelah_penyesuaian ?? $payment->total_pembayaran, 0, ',', '.') . "\n";

} else {
    echo "❌ Payment not found\n";
}

?>
