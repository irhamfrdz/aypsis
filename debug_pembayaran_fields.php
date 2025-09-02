<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get pembayaran data
$pembayaran = DB::table('pembayaran_pranota_kontainer')->where('id', 2)->first();

if ($pembayaran) {
    echo "=== PEMBAYARAN DATA DEBUG ===\n";
    echo "ID: " . $pembayaran->id . "\n";
    echo "Nomor Pembayaran: " . $pembayaran->nomor_pembayaran . "\n";
    echo "Tanggal Pembayaran: " . ($pembayaran->tanggal_pembayaran ?? 'NULL') . "\n";
    echo "Tanggal Kas: " . ($pembayaran->tanggal_kas ?? 'NULL') . "\n";
    echo "Bank: " . ($pembayaran->bank ?? 'NULL') . "\n";
    echo "Jenis Transaksi: " . ($pembayaran->jenis_transaksi ?? 'NULL') . "\n";
    echo "Total Pembayaran: " . ($pembayaran->total_pembayaran ?? 'NULL') . "\n";
    echo "================================\n";
} else {
    echo "Pembayaran with ID 2 not found\n";
}

// Check table structure
echo "\n=== TABLE STRUCTURE ===\n";
$columns = Schema::getColumnListing('pembayaran_pranota_kontainer');
foreach ($columns as $column) {
    echo "Column: " . $column . "\n";
}
