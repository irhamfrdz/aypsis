<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "=== Pembersihan Database Pranota ===\n\n";

try {
    DB::beginTransaction();

    // 1. Cek data yang akan dihapus
    echo "1. Mengecek data yang akan dihapus:\n";

    $pranotaCount = DB::table('pranotalist')->count();
    echo "   - Pranota: {$pranotaCount} records\n";

    $pembayaranCount = DB::table('pembayaran_pranota_kontainer')->count();
    echo "   - Pembayaran Pranota Kontainer: {$pembayaranCount} records\n";

    $pembayaranItemsCount = DB::table('pembayaran_pranota_kontainer_items')->count();
    echo "   - Pembayaran Pranota Kontainer Items: {$pembayaranItemsCount} records\n";

    // 2. Tampilkan detail pranota yang akan dihapus
    echo "\n2. Detail pranota yang akan dihapus:\n";
    $pranotaList = DB::table('pranotalist')->select('id', 'no_invoice', 'status', 'total_amount')->get();
    foreach ($pranotaList as $pranota) {
        echo "   - ID: {$pranota->id}, No: {$pranota->no_invoice}, Status: {$pranota->status}, Amount: {$pranota->total_amount}\n";
    }

    // 3. Konfirmasi
    echo "\n3. Apakah Anda yakin ingin menghapus semua data pranota? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    $confirmation = trim(strtolower($line));

    if ($confirmation === 'y' || $confirmation === 'yes') {
        echo "\n4. Memulai pembersihan...\n";

        // Hapus dalam urutan yang benar (child tables dulu)

        // 4a. Hapus pembayaran items
        $deletedItems = DB::table('pembayaran_pranota_kontainer_items')->delete();
        echo "   ✅ Deleted {$deletedItems} pembayaran items\n";

        // 4b. Hapus pembayaran
        $deletedPembayaran = DB::table('pembayaran_pranota_kontainer')->delete();
        echo "   ✅ Deleted {$deletedPembayaran} pembayaran records\n";

        // 4c. Hapus pranota
        $deletedPranota = DB::table('pranotalist')->delete();
        echo "   ✅ Deleted {$deletedPranota} pranota records\n";

        // 5. Reset auto increment
        echo "\n5. Reset auto increment...\n";
        DB::statement('ALTER TABLE pranotalist AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE pembayaran_pranota_kontainer AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE pembayaran_pranota_kontainer_items AUTO_INCREMENT = 1');
        echo "   ✅ Auto increment reset\n";

        DB::commit();

        echo "\n✅ Pembersihan database pranota berhasil!\n";
        echo "   - Total pranota dihapus: {$deletedPranota}\n";
        echo "   - Total pembayaran dihapus: {$deletedPembayaran}\n";
        echo "   - Total pembayaran items dihapus: {$deletedItems}\n";

    } else {
        DB::rollback();
        echo "\n❌ Pembersihan dibatalkan.\n";
    }

} catch (Exception $e) {
    DB::rollback();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Selesai ===\n";
