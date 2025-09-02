<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;
use App\Models\PembayaranPranotaKontainer;
use App\Models\PembayaranPranotaKontainerItem;

// Get a pranota with 'sent' status to create payment for
$pranota = Pranota::where('status', 'sent')->first();

if ($pranota) {
    echo "Creating payment for pranota: " . $pranota->no_invoice . "\n";

    // Create a payment
    $pembayaran = PembayaranPranotaKontainer::create([
        'nomor_pembayaran' => 'BPK-1-25-09-000001',
        'nomor_cetakan' => 1,
        'bank' => 'BCA',
        'jenis_transaksi' => 'transfer',
        'tanggal_kas' => now()->toDateString(),
        'tanggal_pembayaran' => now()->toDateString(),
        'total_pembayaran' => $pranota->total_amount,
        'penyesuaian' => 0,
        'total_setelah_penyesuaian' => $pranota->total_amount,
        'status' => 'approved',
        'dibuat_oleh' => 1,
        'disetujui_oleh' => 1,
        'tanggal_persetujuan' => now()
    ]);

    // Create payment item
    PembayaranPranotaKontainerItem::create([
        'pembayaran_pranota_kontainer_id' => $pembayaran->id,
        'pranota_id' => $pranota->id,
        'amount' => $pranota->total_amount
    ]);

    // Update pranota status to paid
    $pranota->update(['status' => 'paid']);

    echo "Payment created with ID: " . $pembayaran->id . "\n";
    echo "Pranota status updated to: " . $pranota->status . "\n";
    echo "Payment date: " . $pembayaran->tanggal_persetujuan . "\n";
} else {
    echo "No pranota with 'sent' status found\n";
}
