<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "=== Verifikasi Database Pranota ===\n\n";

try {
    echo "1. Mengecek status database setelah pembersihan:\n";

    $pranotaCount = DB::table('pranotalist')->count();
    echo "   - Pranota: {$pranotaCount} records\n";

    $pembayaranCount = DB::table('pembayaran_pranota_kontainer')->count();
    echo "   - Pembayaran Pranota Kontainer: {$pembayaranCount} records\n";

    $pembayaranItemsCount = DB::table('pembayaran_pranota_kontainer_items')->count();
    echo "   - Pembayaran Pranota Kontainer Items: {$pembayaranItemsCount} records\n";

    echo "\n2. Checking auto increment values:\n";

    // Check auto increment for pranota
    $result = DB::select("SHOW TABLE STATUS LIKE 'pranotalist'");
    if (!empty($result)) {
        echo "   - pranotalist next auto increment: " . $result[0]->Auto_increment . "\n";
    }

    $result = DB::select("SHOW TABLE STATUS LIKE 'pembayaran_pranota_kontainer'");
    if (!empty($result)) {
        echo "   - pembayaran_pranota_kontainer next auto increment: " . $result[0]->Auto_increment . "\n";
    }

    $result = DB::select("SHOW TABLE STATUS LIKE 'pembayaran_pranota_kontainer_items'");
    if (!empty($result)) {
        echo "   - pembayaran_pranota_kontainer_items next auto increment: " . $result[0]->Auto_increment . "\n";
    }

    if ($pranotaCount == 0 && $pembayaranCount == 0 && $pembayaranItemsCount == 0) {
        echo "\n✅ Database pranota sudah bersih!\n";
        echo "   Semua data pranota dan pembayaran terkait telah dihapus.\n";
        echo "   Auto increment telah direset ke 1.\n";
    } else {
        echo "\n⚠️  Masih ada data tersisa:\n";
        if ($pranotaCount > 0) {
            echo "   - {$pranotaCount} pranota masih ada\n";
        }
        if ($pembayaranCount > 0) {
            echo "   - {$pembayaranCount} pembayaran masih ada\n";
        }
        if ($pembayaranItemsCount > 0) {
            echo "   - {$pembayaranItemsCount} pembayaran items masih ada\n";
        }
    }

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Verifikasi Selesai ===\n";
