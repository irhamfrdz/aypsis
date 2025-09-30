<?php

// Test script to manually create pembayaran pranota CAT
echo "Testing manual pembayaran pranota CAT creation..." . PHP_EOL;

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Get unpaid pranota
    $pranota = \App\Models\PranotaTagihanCat::where('status', 'unpaid')->first();

    if (!$pranota) {
        echo "No unpaid pranota found!" . PHP_EOL;
        exit;
    }

    echo "Found pranota: {$pranota->no_invoice} (ID: {$pranota->id})" . PHP_EOL;

    // Create pembayaran
    $pembayaran = \App\Models\PembayaranPranotaCat::create([
        'nomor_pembayaran' => 'TEST-' . time(),
        'bank' => 'Test Bank',
        'jenis_transaksi' => 'debit',
        'tanggal_kas' => now(),
        'total_pembayaran' => $pranota->total_amount,
        'penyesuaian' => 0,
        'total_setelah_penyesuaian' => $pranota->total_amount,
        'status' => 'approved'
    ]);

    echo "Pembayaran created with ID: {$pembayaran->id}" . PHP_EOL;

    // Create payment item
    $item = \App\Models\PembayaranPranotaCatItem::create([
        'pembayaran_pranota_cat_id' => $pembayaran->id,
        'pranota_tagihan_cat_id' => $pranota->id,
        'amount' => $pranota->total_amount
    ]);

    echo "Payment item created with ID: {$item->id}" . PHP_EOL;

    // Update pranota status
    $pranota->update(['status' => 'paid']);
    echo "Pranota status updated to paid" . PHP_EOL;

    echo "SUCCESS: Manual pembayaran creation completed!" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
