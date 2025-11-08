<?php
/**
 * Script untuk memeriksa struktur tabel pembayaran_pranota_uang_jalans
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STRUKTUR TABEL PEMBAYARAN_PRANOTA_UANG_JALANS ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Cek kolom yang ada di tabel pembayaran_pranota_uang_jalans
    $columns = DB::select("SHOW COLUMNS FROM pembayaran_pranota_uang_jalans");
    
    echo "Kolom yang tersedia di tabel pembayaran_pranota_uang_jalans:\n";
    foreach ($columns as $column) {
        echo sprintf(
            "- %s (%s) %s %s %s\n",
            $column->Field,
            $column->Type,
            $column->Null === 'YES' ? 'NULL' : 'NOT NULL',
            $column->Key ? "KEY: {$column->Key}" : '',
            $column->Default ? "DEFAULT: {$column->Default}" : ''
        );
    }
    
    echo "\n=== FOKUS KOLOM JENIS_TRANSAKSI ===\n";
    
    // Cek khusus kolom jenis_transaksi
    $jenisTransaksiColumn = collect($columns)->firstWhere('Field', 'jenis_transaksi');
    
    if ($jenisTransaksiColumn) {
        echo "Kolom jenis_transaksi:\n";
        echo "- Type: " . $jenisTransaksiColumn->Type . "\n";
        echo "- Null: " . ($jenisTransaksiColumn->Null === 'YES' ? 'YES' : 'NO') . "\n";
        echo "- Default: " . ($jenisTransaksiColumn->Default ?? 'NULL') . "\n";
        
        // Cek panjang maksimal jika VARCHAR
        if (preg_match('/varchar\((\d+)\)/', $jenisTransaksiColumn->Type, $matches)) {
            $maxLength = $matches[1];
            echo "- Max Length: " . $maxLength . " karakter\n";
            
            echo "\nAnalisis nilai:\n";
            echo "- 'Kredit' = " . strlen('Kredit') . " karakter\n";
            echo "- 'Debit' = " . strlen('Debit') . " karakter\n";
            
            if ($maxLength < 6) {
                echo "\n❌ MASALAH DITEMUKAN: Kolom terlalu kecil!\n";
                echo "Solusi: Ubah tipe kolom menjadi VARCHAR(10) atau lebih besar\n";
            } else {
                echo "\n✅ Ukuran kolom cukup untuk nilai 'Kredit' dan 'Debit'\n";
            }
        }
    } else {
        echo "❌ Kolom jenis_transaksi tidak ditemukan!\n";
    }
    
    echo "\n=== SAMPLE DATA PEMBAYARAN (Jika Ada) ===\n";
    
    // Ambil sample data untuk melihat format yang ada
    $payments = DB::table('pembayaran_pranota_uang_jalans')
        ->select('id', 'nomor_pembayaran', 'jenis_transaksi', 'bank')
        ->limit(5)
        ->get();
    
    if ($payments->isEmpty()) {
        echo "Belum ada data pembayaran.\n";
    } else {
        echo "Sample data pembayaran:\n";
        foreach ($payments as $payment) {
            echo sprintf(
                "ID: %s | Nomor: %s | Jenis: '%s' | Bank: %s\n",
                $payment->id,
                $payment->nomor_pembayaran,
                $payment->jenis_transaksi,
                substr($payment->bank ?? 'NULL', 0, 30) . '...'
            );
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";