<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UangJalan;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;

echo "=== Verifikasi Unifikasi Uang Jalan ===\n\n";

// Total uang jalan
$totalUangJalan = UangJalan::count();
echo "Total Uang Jalan: {$totalUangJalan}\n\n";

// Uang jalan untuk surat jalan biasa
$uangJalanBiasa = UangJalan::whereNotNull('surat_jalan_id')->count();
echo "Uang Jalan untuk Surat Jalan Biasa: {$uangJalanBiasa}\n";

// Uang jalan untuk surat jalan bongkaran
$uangJalanBongkaran = UangJalan::whereNotNull('surat_jalan_bongkaran_id')->count();
echo "Uang Jalan untuk Surat Jalan Bongkaran: {$uangJalanBongkaran}\n\n";

echo "Detail Uang Jalan Bongkaran:\n";
echo str_repeat("=", 120) . "\n";
printf("%-5s %-25s %-25s %-20s %-20s %-15s\n", 
    "ID", 
    "Nomor UJ", 
    "Nomor SJ Bongkaran",
    "Supir",
    "Tanggal",
    "Total"
);
echo str_repeat("=", 120) . "\n";

$uangJalansBongkaran = UangJalan::whereNotNull('surat_jalan_bongkaran_id')
    ->with('suratJalanBongkaran')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($uangJalansBongkaran as $uj) {
    $sj = $uj->suratJalanBongkaran;
    
    printf("%-5s %-25s %-25s %-20s %-20s %-15s\n",
        $uj->id,
        substr($uj->nomor_uang_jalan ?? 'N/A', 0, 25),
        substr($sj->nomor_surat_jalan ?? 'N/A', 0, 25),
        substr($sj->supir ?? 'N/A', 0, 20),
        $uj->tanggal_uang_jalan ? $uj->tanggal_uang_jalan->format('d/m/Y') : 'N/A',
        number_format($uj->jumlah_total ?? 0, 0, ',', '.')
    );
}

echo str_repeat("=", 120) . "\n\n";

// Cek surat jalan bongkaran yang belum ada uang jalannya
$sjBongkaranWithoutUJ = SuratJalanBongkaran::whereDoesntHave('uangJalan')->count();
echo "Surat Jalan Bongkaran yang BELUM ada Uang Jalan: {$sjBongkaranWithoutUJ}\n";

// Cek surat jalan biasa yang belum ada uang jalannya
$sjBiasaWithoutUJ = SuratJalan::where('status_pembayaran_uang_jalan', 'belum_ada')
    ->whereNotNull('order_id')
    ->where(function($q) {
        $q->whereNull('is_supir_customer')
          ->orWhere('is_supir_customer', false)
          ->orWhere('is_supir_customer', 0);
    })
    ->count();
echo "Surat Jalan Biasa yang BELUM ada Uang Jalan: {$sjBiasaWithoutUJ}\n\n";

echo "✓ Verifikasi selesai!\n";
echo "\nSemua uang jalan (biasa dan bongkaran) sekarang tersimpan di satu tabel: uang_jalans\n";

echo "\n";
