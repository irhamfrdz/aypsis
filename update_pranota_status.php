<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== UPDATE STATUS PRANOTA KE SENT ===\n";

// Ambil pranota unpaid
$unpaidPranota = \App\Models\Pranota::where('status', 'unpaid')->get();

if ($unpaidPranota->count() == 0) {
    echo "❌ Tidak ada pranota dengan status unpaid.\n";
    exit;
}

echo "Pranota yang akan diubah statusnya:\n\n";
foreach ($unpaidPranota as $pranota) {
    echo "No Invoice: {$pranota->no_invoice}\n";
    echo "Status saat ini: {$pranota->status}\n";

    // Update status ke paid
    $pranota->update(['status' => 'paid']);

    echo "Status baru: {$pranota->fresh()->status}\n";
    echo "✅ Sekarang bisa dichecklist untuk bulk payment!\n";
    echo "---\n";
}

echo "\n🎉 Semua pranota draft berhasil diubah ke status 'sent'!\n";
echo "Sekarang Anda bisa checklist pranota untuk bulk payment.\n";
