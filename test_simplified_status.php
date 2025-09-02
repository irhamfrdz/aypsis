<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

echo "=== Test Simplified Pranota Status System ===\n\n";

// Check current pranota
$pranota = Pranota::first();
if ($pranota) {
    echo "Pranota found:\n";
    echo "- No Invoice: {$pranota->no_invoice}\n";
    echo "- Status: {$pranota->status}\n";
    echo "- Status Label: {$pranota->getStatusLabel()}\n";
    echo "- Simple Payment Status: {$pranota->getSimplePaymentStatus()}\n";
    echo "- Payment Status Color: {$pranota->getSimplePaymentStatusColor()}\n";

    echo "\nTesting status change to 'paid':\n";
    $pranota->update(['status' => 'paid']);
    echo "- Status: {$pranota->status}\n";
    echo "- Status Label: {$pranota->getStatusLabel()}\n";
    echo "- Simple Payment Status: {$pranota->getSimplePaymentStatus()}\n";
    echo "- Payment Status Color: {$pranota->getSimplePaymentStatusColor()}\n";

    echo "\nReverting back to 'unpaid':\n";
    $pranota->update(['status' => 'unpaid']);
    $pranota->refresh(); // Refresh model from database
    echo "- Status: {$pranota->status}\n";
    echo "- Status Label: {$pranota->getStatusLabel()}\n";
    echo "- Simple Payment Status: {$pranota->getSimplePaymentStatus()}\n";

    // Debug the match function
    echo "\nDebug match function:\n";
    $status = $pranota->status;
    echo "- Raw status: '{$status}'\n";
    echo "- Status comparison: " . ($status === 'unpaid' ? 'true' : 'false') . "\n";
    echo "- Match result: " . match($status) {
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Sudah Dibayar',
        default => 'DEFAULT: ' . ucfirst($status)
    } . "\n";

} else {
    echo "No pranota found\n";
}

echo "\n=== Test Complete ===\n";
