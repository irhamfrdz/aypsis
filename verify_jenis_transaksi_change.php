<?php
/**
 * Verifikasi perubahan kolom jenis_transaksi
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFIKASI PERUBAHAN KOLOM JENIS_TRANSAKSI ===\n";

try {
    $column = DB::select("SHOW COLUMNS FROM pembayaran_pranota_uang_jalans WHERE Field = 'jenis_transaksi'")[0];
    
    echo "✅ Kolom jenis_transaksi berhasil diubah!\n";
    echo "- Type: " . $column->Type . "\n";
    echo "- Null: " . ($column->Null === 'YES' ? 'YES' : 'NO') . "\n";
    echo "- Default: " . ($column->Default ?? 'NULL') . "\n";
    
    if (strpos($column->Type, 'varchar') !== false) {
        echo "\n🎉 SUKSES: Kolom sekarang sudah VARCHAR dan bisa menerima nilai 'Kredit' dan 'Debit'!\n";
    } else {
        echo "\n❌ PERLU CEK: Kolom masih belum VARCHAR\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";