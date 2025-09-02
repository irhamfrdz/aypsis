<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Test Pranota Model After Cleanup ===\n\n";

try {
    echo "1. Testing Pranota model...\n";

    $count = App\Models\Pranota::count();
    echo "   - Pranota count: {$count}\n";

    echo "2. Testing create new pranota...\n";
    $pranota = App\Models\Pranota::create([
        'no_invoice' => 'TEST001',
        'total_amount' => 1000000.00,
        'keterangan' => 'Test pranota after cleanup',
        'status' => 'draft',
        'tagihan_ids' => [1, 2, 3],
        'jumlah_tagihan' => 3,
        'tanggal_pranota' => today(),
        'due_date' => today()->addDays(30)
    ]);

    echo "   ✅ Created test pranota with ID: {$pranota->id}\n";
    echo "   - No Invoice: {$pranota->no_invoice}\n";
    echo "   - Status: {$pranota->status}\n";
    echo "   - Amount: " . number_format($pranota->total_amount, 2) . "\n";

    echo "\n3. Testing model methods...\n";
    if (method_exists($pranota, 'getSimplePaymentStatus')) {
        echo "   - Payment Status: " . $pranota->getSimplePaymentStatus() . "\n";
    }

    if (method_exists($pranota, 'hasPaymentPending')) {
        $pending = $pranota->hasPaymentPending();
        echo "   - Has Pending Payment: " . ($pending ? 'Yes' : 'No') . "\n";
    }

    echo "\n4. Cleaning up test data...\n";
    $pranota->delete();
    echo "   ✅ Test pranota deleted\n";

    $finalCount = App\Models\Pranota::count();
    echo "   - Final count: {$finalCount}\n";

    echo "\n✅ Pranota model working perfectly after cleanup!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Selesai ===\n";
