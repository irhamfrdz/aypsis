<?php

/**
 * Test script untuk memeriksa data pengirim dan jenis barang pada order
 * Jalankan dengan: php test_order_relations.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Order;
use App\Models\Pengirim;
use App\Models\JenisBarang;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Order Relations ===\n\n";

// Test 1: Cek data pengirim
echo "1. Testing Pengirim Model:\n";
$pengirims = Pengirim::take(3)->get();
foreach ($pengirims as $pengirim) {
    echo "   ID: {$pengirim->id}, Nama: {$pengirim->nama_pengirim}\n";
}
echo "\n";

// Test 2: Cek data jenis barang
echo "2. Testing JenisBarang Model:\n";
$jenisBarangs = JenisBarang::take(3)->get();
foreach ($jenisBarangs as $jenis) {
    echo "   ID: {$jenis->id}, Nama: {$jenis->nama_barang}\n";
}
echo "\n";

// Test 3: Cek order dengan relations
echo "3. Testing Order with Relations:\n";
$orders = Order::with(['pengirim', 'jenisBarang'])
             ->whereNotNull('pengirim_id')
             ->whereNotNull('jenis_barang_id')
             ->take(3)
             ->get();

if ($orders->count() > 0) {
    foreach ($orders as $order) {
        echo "   Order: {$order->nomor_order}\n";
        echo "   Pengirim: " . ($order->pengirim ? $order->pengirim->nama_pengirim : 'NULL') . "\n";
        echo "   Jenis Barang: " . ($order->jenisBarang ? $order->jenisBarang->nama_barang : 'NULL') . "\n";
        echo "   ---\n";
    }
} else {
    echo "   Tidak ada order dengan pengirim_id dan jenis_barang_id\n";
}
echo "\n";

// Test 4: Cek struktur kolom tabel orders
echo "4. Testing Order Table Structure:\n";
$order = Order::first();
if ($order) {
    echo "   Sample Order Attributes:\n";
    echo "   - pengirim_id: " . ($order->pengirim_id ?? 'NULL') . "\n";
    echo "   - jenis_barang_id: " . ($order->jenis_barang_id ?? 'NULL') . "\n";

    if ($order->pengirim_id) {
        $pengirim = Pengirim::find($order->pengirim_id);
        echo "   - Pengirim exists: " . ($pengirim ? "YES ({$pengirim->nama_pengirim})" : "NO") . "\n";
    }

    if ($order->jenis_barang_id) {
        $jenisBarang = JenisBarang::find($order->jenis_barang_id);
        echo "   - JenisBarang exists: " . ($jenisBarang ? "YES ({$jenisBarang->nama_barang})" : "NO") . "\n";
    }
}

echo "\n=== Test Completed ===\n";
