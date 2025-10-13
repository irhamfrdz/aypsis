<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MasterPricelistSewaKontainer;

echo "=== MENAMBAH PRICELIST UNTUK DESEMBER 2024 ===\n\n";

// Cek pricelist yang ada
echo "📋 PRICELIST ZONA 40 YANG ADA:\n";
$existing = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
    ->where('ukuran_kontainer', '40')
    ->orderBy('tanggal_harga_awal')
    ->get();

foreach ($existing as $pl) {
    echo "ID: {$pl->id} | Tarif: {$pl->tarif} | Harga: Rp " . number_format((float)$pl->harga, 0, ',', '.') . " | Berlaku: {$pl->tanggal_harga_awal}\n";
}

echo "\n";

// Tambah pricelist untuk Desember 2024
echo "➕ MENAMBAH PRICELIST DESEMBER 2024:\n";

try {
    // Harian
    $newHarian = new MasterPricelistSewaKontainer();
    $newHarian->vendor = 'ZONA';
    $newHarian->tarif = 'Harian';
    $newHarian->ukuran_kontainer = '40';
    $newHarian->harga = 42042.00;
    $newHarian->tanggal_harga_awal = '2024-12-01 00:00:00';
    $newHarian->save();

    echo "✅ Pricelist Harian ditambahkan (ID: {$newHarian->id})\n";

    // Bulanan
    $newBulanan = new MasterPricelistSewaKontainer();
    $newBulanan->vendor = 'ZONA';
    $newBulanan->tarif = 'Bulanan';
    $newBulanan->ukuran_kontainer = '40';
    $newBulanan->harga = 1261261.00;
    $newBulanan->tanggal_harga_awal = '2024-12-01 00:00:00';
    $newBulanan->save();

    echo "✅ Pricelist Bulanan ditambahkan (ID: {$newBulanan->id})\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n📋 PRICELIST SETELAH DITAMBAH:\n";
$updated = MasterPricelistSewaKontainer::where('vendor', 'ZONA')
    ->where('ukuran_kontainer', '40')
    ->orderBy('tanggal_harga_awal')
    ->get();

foreach ($updated as $pl) {
    echo "ID: {$pl->id} | Tarif: {$pl->tarif} | Harga: Rp " . number_format((float)$pl->harga, 0, ',', '.') . " | Berlaku: {$pl->tanggal_harga_awal}\n";
}

echo "\n=== SELESAI ===\n";
