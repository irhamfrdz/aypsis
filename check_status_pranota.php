<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK STATUS PRANOTA ===\n";

// Periksa semua pranota dan statusnya
$pranotaList = \App\Models\Pranota::orderBy('created_at', 'desc')->limit(10)->get();

if ($pranotaList->count() == 0) {
    echo "❌ Tidak ada pranota ditemukan.\n";
    exit;
}

echo "Status pranota Anda:\n\n";
foreach ($pranotaList as $pranota) {
    $checkboxStatus = $pranota->status == 'sent' ? '✅ Bisa dichecklist' : '❌ Tidak bisa dichecklist';
    echo "No Invoice: {$pranota->no_invoice}\n";
    echo "Status: {$pranota->status}\n";
    echo "Checkbox: {$checkboxStatus}\n";
    echo "---\n";
}

echo "\n📝 CATATAN:\n";
echo "Checkbox hanya muncul untuk pranota dengan status 'sent'.\n";
echo "Status lain ('draft', 'paid', 'cancelled') tidak menampilkan checkbox.\n\n";

// Hitung distribusi status
$statusCount = \App\Models\Pranota::selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

echo "Distribusi status pranota:\n";
foreach ($statusCount as $status) {
    echo "- {$status->status}: {$status->count} pranota\n";
}
