<?php
/**
 * Script untuk memeriksa struktur tabel tujuan_kegiatan_utamas
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STRUKTUR TABEL TUJUAN_KEGIATAN_UTAMAS ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Cek kolom yang ada di tabel tujuan_kegiatan_utamas
    $columns = DB::select("SHOW COLUMNS FROM tujuan_kegiatan_utamas");
    
    echo "Kolom yang tersedia di tabel tujuan_kegiatan_utamas:\n";
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
    
    echo "\n=== SAMPLE DATA TUJUAN ===\n";
    
    // Ambil sample data dari tabel tujuan_kegiatan_utamas
    $tujuans = DB::table('tujuan_kegiatan_utamas')
        ->limit(10)
        ->get();
    
    if ($tujuans->isEmpty()) {
        echo "Tidak ada data tujuan.\n";
    } else {
        echo "Sample 10 tujuan pertama:\n";
        foreach ($tujuans as $tujuan) {
            $tujuanArray = (array) $tujuan;
            echo "Tujuan ID {$tujuan->id}:\n";
            foreach ($tujuanArray as $field => $value) {
                echo "  {$field}: " . ($value ?? 'NULL') . "\n";
            }
            echo "\n";
        }
    }
    
    echo "\n=== SAMPLE DATA SURAT JALAN DENGAN TUJUAN ===\n";
    
    // Ambil sample data surat jalan untuk melihat bagaimana tujuan disimpan
    $suratJalans = DB::table('surat_jalans')
        ->select('id', 'no_surat_jalan', 'tujuan_pengambilan', 'tujuan_pengiriman', 'tujuan')
        ->whereNotNull('tujuan_pengambilan')
        ->orWhereNotNull('tujuan_pengiriman') 
        ->orWhereNotNull('tujuan')
        ->limit(10)
        ->get();
    
    if ($suratJalans->isEmpty()) {
        echo "Tidak ada surat jalan dengan data tujuan.\n";
    } else {
        echo "Sample surat jalan dengan tujuan:\n";
        foreach ($suratJalans as $sj) {
            echo sprintf(
                "SJ ID: %s | No: %s | Tujuan Ambil: %s | Tujuan Kirim: %s | Tujuan: %s\n",
                $sj->id,
                $sj->no_surat_jalan ?? 'NULL',
                $sj->tujuan_pengambilan ?? 'NULL',
                $sj->tujuan_pengiriman ?? 'NULL',
                $sj->tujuan ?? 'NULL'
            );
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";