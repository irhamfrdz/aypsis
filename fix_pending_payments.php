<?php
/**
 * Script untuk memperbaiki status pembayaran yang masih pending
 * Mengubah status dari 'pending' ke 'paid' untuk pembayaran yang sudah berhasil disimpan
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use App\Models\PembayaranPranotaUangJalan;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Perbaikan Status Pembayaran Pending ===\n\n";

// Cari semua pembayaran dengan status pending
$pendingPayments = PembayaranPranotaUangJalan::where('status_pembayaran', 'pending')->get();

echo "Ditemukan {$pendingPayments->count()} pembayaran dengan status 'pending'\n\n";

if ($pendingPayments->count() > 0) {
    echo "Detail pembayaran yang akan diperbaiki:\n";
    foreach ($pendingPayments as $payment) {
        echo "- ID: {$payment->id}, Nomor: {$payment->nomor_pembayaran}, Tanggal: {$payment->tanggal_pembayaran}\n";
    }
    
    echo "\nProses perbaikan...\n";
    
    $updated = 0;
    foreach ($pendingPayments as $payment) {
        try {
            $payment->update(['status_pembayaran' => PembayaranPranotaUangJalan::STATUS_PAID]);
            echo "✓ Berhasil update pembayaran ID: {$payment->id} ke status 'paid'\n";
            $updated++;
        } catch (Exception $e) {
            echo "✗ Gagal update pembayaran ID: {$payment->id} - Error: {$e->getMessage()}\n";
        }
    }
    
    echo "\n=== Hasil Perbaikan ===\n";
    echo "Total pembayaran diperbaiki: {$updated}\n";
    echo "Status pembayaran sekarang:\n";
    
    $pendingCount = PembayaranPranotaUangJalan::where('status_pembayaran', 'pending')->count();
    $paidCount = PembayaranPranotaUangJalan::where('status_pembayaran', 'paid')->count();
    $cancelledCount = PembayaranPranotaUangJalan::where('status_pembayaran', 'cancelled')->count();
    
    echo "- Pending: {$pendingCount}\n";
    echo "- Paid: {$paidCount}\n";
    echo "- Cancelled: {$cancelledCount}\n";
    
} else {
    echo "Tidak ada pembayaran dengan status 'pending' yang perlu diperbaiki.\n";
}

echo "\n=== Perbaikan Selesai ===\n";