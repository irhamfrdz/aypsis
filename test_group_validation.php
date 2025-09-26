<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Test Validasi Grup untuk Masukan ke Pranota\n";
echo "==========================================\n";

// Ambil beberapa data untuk test
$tagihans = DaftarTagihanKontainerSewa::limit(10)->get();

echo "Data yang tersedia:\n";
foreach ($tagihans as $tagihan) {
    $hasGroup = !empty($tagihan->group) && $tagihan->group !== '-';
    echo "- ID {$tagihan->id}: {$tagihan->nomor_kontainer} | Grup: " . ($hasGroup ? $tagihan->group : 'TIDAK ADA') . "\n";
}

echo "\nSimulasi validasi grup:\n";
echo "=======================\n";

// Simulasi item yang dipilih (misalnya ID 1, 2, 3)
$selectedIds = [1, 2, 3]; // Ganti dengan ID yang ada di database Anda

$itemsWithoutGroup = [];
$itemsWithGroup = [];

foreach ($selectedIds as $id) {
    $tagihan = DaftarTagihanKontainerSewa::find($id);
    if ($tagihan) {
        $hasGroup = !empty($tagihan->group) && $tagihan->group !== '-';
        if ($hasGroup) {
            $itemsWithGroup[] = $tagihan->nomor_kontainer . " (Grup: {$tagihan->group})";
        } else {
            $itemsWithoutGroup[] = $tagihan->nomor_kontainer;
        }
    }
}

echo "Item yang dipilih: " . implode(', ', array_merge($itemsWithGroup, $itemsWithoutGroup)) . "\n";
echo "Item dengan grup: " . count($itemsWithGroup) . "\n";
echo "Item tanpa grup: " . count($itemsWithoutGroup) . "\n";

if (count($itemsWithoutGroup) > 0) {
    echo "\n❌ TIDAK BISA MASUKAN KE PRANOTA\n";
    echo "Item yang belum memiliki grup:\n";
    foreach ($itemsWithoutGroup as $item) {
        echo "- $item\n";
    }
    echo "\nSilakan buat grup terlebih dahulu!\n";
} else {
    echo "\n✅ BISA MASUKAN KE PRANOTA\n";
    echo "Semua item sudah memiliki grup.\n";
}

echo "\nTest selesai!\n";
