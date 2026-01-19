<?php

use App\Models\PranotaUangRit;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Memulai proses perbaikan status pembayaran rit...\n";

// Ambil semua pranota yang aktif (bukan cancelled)
$pranotas = PranotaUangRit::where('status', '!=', 'cancelled')->get();

echo "Ditemukan " . $pranotas->count() . " pranota aktif.\n";

$totalUpdatedSuratJalan = 0;
$totalUpdatedBongkaran = 0;

foreach ($pranotas as $pranota) {
    if (empty($pranota->no_surat_jalan)) {
        continue;
    }

    // Parse nomor surat jalan (dipisahkan koma)
    $nomorSjList = array_map('trim', explode(',', $pranota->no_surat_jalan));
    
    // Pisahkan antara bongkaran (biasanya ada suffix '(Bongkaran)') dan reguler
    $nomorSjReguler = [];
    $nomorSjBongkaran = [];
    
    foreach ($nomorSjList as $nomor) {
        if (str_contains(strtolower($nomor), '(bongkaran)')) {
            // Hapus suffix ' (Bongkaran)' untuk mendapatkan nomor asli
            $cleanNomor = trim(str_ireplace('(bongkaran)', '', $nomor));
            $nomorSjBongkaran[] = $cleanNomor;
        } else {
            $nomorSjReguler[] = $nomor;
        }
    }
    
    // 1. Update Surat Jalan Reguler
    if (!empty($nomorSjReguler)) {
        $updated = SuratJalan::whereIn('no_surat_jalan', $nomorSjReguler)
            ->where('status_pembayaran_uang_rit', '!=', SuratJalan::STATUS_UANG_RIT_DIBAYAR)
            ->update(['status_pembayaran_uang_rit' => SuratJalan::STATUS_UANG_RIT_DIBAYAR]);
            
        if ($updated > 0) {
            echo "Pranota {$pranota->no_pranota}: Updated {$updated} Surat Jalan reguler menjadi 'dibayar'.\n";
            $totalUpdatedSuratJalan += $updated;
        }
    }
    
    // 2. Update Surat Jalan Bongkaran
    if (!empty($nomorSjBongkaran)) {
        $updatedBongkaran = SuratJalanBongkaran::whereIn('nomor_surat_jalan', $nomorSjBongkaran)
            ->where('status_pembayaran_uang_rit', '!=', 'lunas')
            ->update(['status_pembayaran_uang_rit' => 'lunas']);
            
        if ($updatedBongkaran > 0) {
            echo "Pranota {$pranota->no_pranota}: Updated {$updatedBongkaran} Surat Jalan Bongkaran menjadi 'lunas'.\n";
            $totalUpdatedBongkaran += $updatedBongkaran;
        }
    }
}

echo "\n------------------------------------------------\n";
echo "Total Surat Jalan reguler diperbarui: {$totalUpdatedSuratJalan}\n";
echo "Total Surat Jalan Bongkaran diperbarui: {$totalUpdatedBongkaran}\n";
echo "Selesai.\n";
