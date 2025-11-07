<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Print Pranota Uang Jalan (Updated Format) ===\n\n";

// Cari pranota uang jalan untuk test
$pranota = \App\Models\PranotaUangJalan::with(['uangJalans.suratJalan'])
    ->first();

if (!$pranota) {
    echo "❌ Tidak ada pranota untuk test\n";
    exit;
}

echo "✅ Test data ditemukan:\n";
echo "- Nomor Pranota: {$pranota->nomor_pranota}\n";
echo "- Jumlah Uang Jalan: {$pranota->jumlah_uang_jalan}\n";
echo "- Total Amount: Rp " . number_format($pranota->total_amount, 0, ',', '.') . "\n\n";

echo "=== Preview Data Tabel Print ===\n\n";

$headers = [
    'No', 
    'Nomor Surat Jalan', 
    'Nomor Uang Jalan', 
    'Barang', 
    'NIK', 
    'Supir', 
    'Pengirim', 
    'Tujuan', 
    'No Kas Bank', 
    'Tanggal Tanda Terima', 
    'Total'
];

// Print header
printf("%-3s | %-15s | %-15s | %-12s | %-8s | %-12s | %-12s | %-12s | %-12s | %-12s | %-12s\n", ...$headers);
echo str_repeat('-', 150) . "\n";

foreach ($pranota->uangJalans as $index => $uangJalan) {
    $no = $index + 1;
    $noSuratJalan = $uangJalan->suratJalan ? $uangJalan->suratJalan->no_surat_jalan : '-';
    $nomorUangJalan = $uangJalan->nomor_uang_jalan;
    $barang = $uangJalan->suratJalan ? ($uangJalan->suratJalan->jenis_barang ?: '-') : '-';
    $nik = $uangJalan->suratJalan ? ($uangJalan->suratJalan->karyawan ?: '-') : '-';
    $supir = $uangJalan->suratJalan ? ($uangJalan->suratJalan->supir ?: '-') : '-';
    $pengirim = $uangJalan->suratJalan ? ($uangJalan->suratJalan->pengirim ?: '-') : '-';
    $tujuan = $uangJalan->suratJalan ? ($uangJalan->suratJalan->tujuan_pengiriman ?: '-') : '-';
    $noKasBank = $uangJalan->nomor_kas_bank ?: '-';
    $tanggalTandaTerima = $uangJalan->suratJalan && $uangJalan->suratJalan->tanggal_tanda_terima ? 
        $uangJalan->suratJalan->tanggal_tanda_terima->format('d/m/Y') : '-';
    $total = 'Rp ' . number_format($uangJalan->jumlah_total, 0, ',', '.');
    
    printf("%-3s | %-15s | %-15s | %-12s | %-8s | %-12s | %-12s | %-12s | %-12s | %-12s | %-12s\n", 
        $no, 
        substr($noSuratJalan, 0, 15),
        substr($nomorUangJalan, 0, 15), 
        substr($barang, 0, 12),
        substr($nik, 0, 8),
        substr($supir, 0, 12),
        substr($pengirim, 0, 12),
        substr($tujuan, 0, 12),
        substr($noKasBank, 0, 12),
        $tanggalTandaTerima,
        substr($total, 0, 12)
    );
}

echo str_repeat('-', 150) . "\n";
echo "TOTAL: Rp " . number_format($pranota->total_amount, 0, ',', '.') . "\n";

echo "\n=== URL untuk Test Print ===\n";
echo "🖨️  /pranota-uang-jalan/{$pranota->id}/print\n\n";

echo "✅ Format tabel sudah diupdate dengan kolom:\n";
foreach ($headers as $i => $header) {
    echo ($i + 1) . ". {$header}\n";
}

echo "\n💡 Tanggal tanda terima akan otomatis terisi setelah approval surat jalan\n";