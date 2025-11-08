<?php
/**
 * Script untuk memeriksa struktur kolom tujuan di surat_jalans
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STRUKTUR KOLOM TUJUAN DI SURAT_JALANS ===\n";

try {
    // Cek semua kolom yang mengandung kata 'tujuan'
    $columns = DB::select("SHOW COLUMNS FROM surat_jalans");
    
    echo "Kolom tujuan yang tersedia:\n";
    foreach ($columns as $column) {
        if (strpos($column->Field, 'tujuan') !== false) {
            echo sprintf(
                "- %s (%s) %s\n",
                $column->Field,
                $column->Type,
                $column->Null === 'YES' ? 'NULL' : 'NOT NULL'
            );
        }
    }
    
    echo "\n=== SAMPLE DATA SURAT JALAN ===\n";
    
    // Ambil sample data surat jalan
    $suratJalans = DB::table('surat_jalans')
        ->select('id', 'no_surat_jalan', 'tujuan_pengambilan', 'tujuan_pengiriman')
        ->whereNotNull('tujuan_pengambilan')
        ->orWhereNotNull('tujuan_pengiriman')
        ->limit(5)
        ->get();
    
    if ($suratJalans->isEmpty()) {
        echo "Tidak ada surat jalan dengan data tujuan.\n";
    } else {
        echo "Sample surat jalan dengan tujuan:\n";
        foreach ($suratJalans as $sj) {
            echo sprintf(
                "SJ ID: %s | No: %s | Tujuan Ambil: %s | Tujuan Kirim: %s\n",
                $sj->id,
                $sj->no_surat_jalan ?? 'NULL',
                $sj->tujuan_pengambilan ?? 'NULL',
                $sj->tujuan_pengiriman ?? 'NULL'
            );
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";