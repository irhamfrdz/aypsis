<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Payment BTK12509000001 ===\n";

// Check pembayaran data
$payment = DB::table('pembayaran_pranota_kontainer')
    ->where('nomor_pembayaran', 'BTK12509000001')
    ->first();

if ($payment) {
    echo "Payment found:\n";
    echo "ID: " . $payment->id . "\n";
    echo "Nomor: " . $payment->nomor_pembayaran . "\n";
    echo "Total Amount: " . $payment->total_amount . "\n";
    echo "Status: " . $payment->status . "\n";
    echo "Created: " . $payment->created_at . "\n";

    // Check items
    echo "\n=== Payment Items ===\n";
    $items = DB::table('pembayaran_pranota_kontainer_items')
        ->where('pembayaran_pranota_kontainer_id', $payment->id)
        ->get();

    foreach ($items as $item) {
        echo "Item ID: " . $item->id . "\n";
        echo "Pranota ID: " . $item->pranota_id . "\n";
        echo "Amount: " . $item->amount . "\n";
        echo "---\n";
    }

    // Check related pranota
    echo "\n=== Related Pranota ===\n";
    $pranotaIds = $items->pluck('pranota_id')->unique();
    foreach ($pranotaIds as $pranotaId) {
        $pranota = DB::table('pranotalist')->where('id', $pranotaId)->first();
        if ($pranota) {
            echo "Pranota ID: " . $pranota->id . "\n";
            echo "No Invoice: " . $pranota->no_invoice . "\n";
            echo "Total Amount: " . $pranota->total_amount . "\n";
            echo "Status: " . $pranota->status . "\n";
            echo "---\n";
        }
    }
} else {
    echo "Payment not found!\n";
}
