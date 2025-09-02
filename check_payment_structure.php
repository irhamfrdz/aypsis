<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Table Structure ===\n";

// Check columns in pembayaran_pranota_kontainer
echo "Columns in pembayaran_pranota_kontainer:\n";
$columns = Schema::getColumnListing('pembayaran_pranota_kontainer');
foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\n=== Sample Payment Data ===\n";
$payment = DB::table('pembayaran_pranota_kontainer')->first();
if ($payment) {
    foreach ($payment as $key => $value) {
        echo "$key: $value\n";
    }
}

echo "\n=== Checking Payment BTK12509000001 ===\n";
$payment = DB::table('pembayaran_pranota_kontainer')
    ->where('nomor_pembayaran', 'BTK12509000001')
    ->first();

if ($payment) {
    echo "Payment found:\n";
    foreach ($payment as $key => $value) {
        echo "$key: $value\n";
    }

    echo "\n=== Payment Items ===\n";
    $items = DB::table('pembayaran_pranota_kontainer_items')
        ->where('pembayaran_pranota_kontainer_id', $payment->id)
        ->get();

    $totalAmount = 0;
    foreach ($items as $item) {
        echo "Item ID: " . $item->id . "\n";
        echo "Pranota ID: " . $item->pranota_id . "\n";
        echo "Amount: " . ($item->amount ?? 0) . "\n";
        $totalAmount += ($item->amount ?? 0);
        echo "---\n";
    }

    echo "Calculated Total: $totalAmount\n";
}
