<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

// Fresh query pranota
$pranota = Pranota::where('status', 'unpaid')->first();
if ($pranota) {
    echo "Fresh pranota query:\n";
    echo "- Status: {$pranota->status}\n";
    echo "- getStatusLabel(): {$pranota->getStatusLabel()}\n";

    // Manual match test
    $status = $pranota->status;
    $label = match($status) {
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Sudah Dibayar',
        default => ucfirst($status)
    };
    echo "- Direct match: {$label}\n";
}
