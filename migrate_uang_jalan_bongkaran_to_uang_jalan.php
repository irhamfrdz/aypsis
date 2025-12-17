<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UangJalan;
use App\Models\UangJalanBongkaran;

echo "=== Migrasi Data Uang Jalan Bongkaran ke Tabel Uang Jalans ===\n\n";

// Ambil semua data dari uang_jalan_bongkarans
$uangJalanBongkarans = UangJalanBongkaran::all();

echo "Total Uang Jalan Bongkaran: " . $uangJalanBongkarans->count() . "\n\n";

if ($uangJalanBongkarans->isEmpty()) {
    echo "Tidak ada data uang jalan bongkaran untuk di-migrasi.\n";
    exit(0);
}

$migrated = 0;
$skipped = 0;
$failed = 0;

echo "Memulai migrasi...\n";
echo str_repeat("=", 120) . "\n";

foreach ($uangJalanBongkarans as $ujb) {
    echo "Processing UJB ID: {$ujb->id}, Nomor: {$ujb->nomor_uang_jalan}\n";
    
    // Cek apakah sudah ada di uang_jalans
    $existing = UangJalan::where('nomor_uang_jalan', $ujb->nomor_uang_jalan)->first();
    
    if ($existing) {
        echo "  - SKIP: Nomor uang jalan sudah ada di tabel uang_jalans (ID: {$existing->id})\n";
        $skipped++;
        continue;
    }
    
    try {
        // Buat record baru di uang_jalans
        $newUJ = UangJalan::create([
            'nomor_uang_jalan' => $ujb->nomor_uang_jalan,
            'tanggal_uang_jalan' => $ujb->tanggal_uang_jalan,
            'surat_jalan_id' => null,
            'surat_jalan_bongkaran_id' => $ujb->surat_jalan_bongkaran_id,
            'kegiatan_bongkar_muat' => $ujb->kegiatan_bongkar_muat,
            'jumlah_uang_jalan' => $ujb->jumlah_uang_jalan,
            'jumlah_mel' => $ujb->jumlah_mel,
            'jumlah_pelancar' => $ujb->jumlah_pelancar,
            'jumlah_kawalan' => $ujb->jumlah_kawalan,
            'jumlah_parkir' => $ujb->jumlah_parkir,
            'subtotal' => $ujb->subtotal,
            'alasan_penyesuaian' => $ujb->alasan_penyesuaian,
            'jumlah_penyesuaian' => $ujb->jumlah_penyesuaian,
            'jumlah_total' => $ujb->jumlah_total,
            'memo' => $ujb->memo,
            'status' => $ujb->status,
            'created_by' => $ujb->created_by,
            'created_at' => $ujb->created_at,
            'updated_at' => $ujb->updated_at,
        ]);
        
        echo "  - SUCCESS: Migrated to uang_jalans ID: {$newUJ->id}\n";
        $migrated++;
        
    } catch (\Exception $e) {
        echo "  - FAILED: " . $e->getMessage() . "\n";
        $failed++;
    }
    
    echo "\n";
}

echo str_repeat("=", 120) . "\n";
echo "\nRingkasan Migrasi:\n";
echo "- Berhasil di-migrasi: {$migrated}\n";
echo "- Dilewati (sudah ada): {$skipped}\n";
echo "- Gagal: {$failed}\n";

if ($migrated > 0) {
    echo "\n✓ Migrasi selesai! Data dari uang_jalan_bongkarans sudah di-copy ke uang_jalans.\n";
    echo "\nCATATAN: Data di tabel uang_jalan_bongkarans BELUM dihapus.\n";
    echo "Jika semua sudah berjalan dengan baik, Anda bisa menghapus data lama secara manual.\n";
}

echo "\n";
