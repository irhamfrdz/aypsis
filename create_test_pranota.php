<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

// Create a test pranota with sent status
$pranota = Pranota::create([
    'no_invoice' => 'PTK12509000001',
    'total_amount' => 5000000,
    'keterangan' => 'Test pranota untuk pembayaran',
    'status' => 'sent',
    'tagihan_ids' => [1, 2],
    'jumlah_tagihan' => 2,
    'tanggal_pranota' => now(),
    'due_date' => now()->addDays(30)
]);

echo 'Created test pranota with ID: ' . $pranota->id . ' and status: ' . $pranota->status . "\n";

// Create another pranota with draft status
$pranota2 = Pranota::create([
    'no_invoice' => 'PTK12509000002',
    'total_amount' => 3000000,
    'keterangan' => 'Test pranota draft',
    'status' => 'draft',
    'tagihan_ids' => [3, 4],
    'jumlah_tagihan' => 2,
    'tanggal_pranota' => now(),
    'due_date' => now()->addDays(30)
]);

echo 'Created test pranota with ID: ' . $pranota2->id . ' and status: ' . $pranota2->status . "\n";

// Create a paid pranota
$pranota3 = Pranota::create([
    'no_invoice' => 'PTK12509000003',
    'total_amount' => 7500000,
    'keterangan' => 'Test pranota sudah dibayar',
    'status' => 'paid',
    'tagihan_ids' => [5, 6],
    'jumlah_tagihan' => 2,
    'tanggal_pranota' => now(),
    'due_date' => now()->addDays(30)
]);

echo 'Created test pranota with ID: ' . $pranota3->id . ' and status: ' . $pranota3->status . "\n";

echo "Test data created successfully!\n";
