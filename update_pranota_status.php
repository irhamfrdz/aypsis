<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== UPDATE STATUS PRANOTA KE SENT ===\n";

// Ambil pranota draft
$draftPranota = \App\Models\Pranota::where('status', 'draft')->get();

if ($draftPranota->count() == 0) {
    echo "❌ Tidak ada pranota dengan status draft.\n";
    exit;
}

echo "Pranota yang akan diubah statusnya:\n\n";
foreach ($draftPranota as $pranota) {
    echo "No Invoice: {$pranota->no_invoice}\n";
    echo "Status saat ini: {$pranota->status}\n";

    // Update status ke sent
    $pranota->update(['status' => 'sent']);

    echo "Status baru: {$pranota->fresh()->status}\n";
    echo "✅ Sekarang bisa dichecklist untuk bulk payment!\n";
    echo "---\n";
}

echo "\n🎉 Semua pranota draft berhasil diubah ke status 'sent'!\n";
echo "Sekarang Anda bisa checklist pranota untuk bulk payment.\n";
