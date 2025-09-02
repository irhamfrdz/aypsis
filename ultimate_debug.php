<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING PRANOTA STATUS LABEL ===\n\n";

try {
    $pranota = \App\Models\Pranota::first();

    echo "1. Basic info:\n";
    echo "   - ID: {$pranota->id}\n";
    echo "   - Raw status: '{$pranota->status}'\n";
    echo "   - Status type: " . gettype($pranota->status) . "\n";

    echo "\n2. Method calls:\n";
    echo "   - getStatusLabel(): '{$pranota->getStatusLabel()}'\n";

    echo "\n3. Status comparison tests:\n";
    echo "   - status === 'paid': " . ($pranota->status === 'paid' ? 'TRUE' : 'FALSE') . "\n";
    echo "   - status === 'unpaid': " . ($pranota->status === 'unpaid' ? 'TRUE' : 'FALSE') . "\n";
    echo "   - status == 'paid': " . ($pranota->status == 'paid' ? 'TRUE' : 'FALSE') . "\n";
    echo "   - status == 'unpaid': " . ($pranota->status == 'unpaid' ? 'TRUE' : 'FALSE') . "\n";

    echo "\n4. Manual logic test:\n";
    $status = $pranota->status;
    $manual_result = ($status === 'paid') ? 'Sudah Dibayar' : 'Belum Dibayar';
    echo "   - Manual logic result: '{$manual_result}'\n";

    echo "\n5. Raw attribute test:\n";
    echo "   - getAttribute('status'): '{$pranota->getAttribute('status')}'\n";
    echo "   - getRawAttribute('status'): '{$pranota->getRawOriginal('status')}'\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== END DEBUG ===\n";
?>
