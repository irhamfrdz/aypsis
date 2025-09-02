<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "Testing hasPaymentPending method...\n";

try {
    $pranota = App\Models\Pranota::first();
    if ($pranota) {
        echo "Found pranota: {$pranota->no_invoice}\n";
        echo "Method exists: " . (method_exists($pranota, 'hasPaymentPending') ? 'Yes' : 'No') . "\n";

        if (method_exists($pranota, 'hasPaymentPending')) {
            $hasPending = $pranota->hasPaymentPending();
            echo "Has payment pending: " . ($hasPending ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "No pranota found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
