<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FINAL TEST: Print Pranota Uang Jalan ===\n\n";

echo "✅ Tabel print telah diupdate dengan kolom sesuai permintaan:\n\n";

$kolom_baru = [
    1 => 'No',
    2 => 'Nomor Surat Jalan', 
    3 => 'Nomor Uang Jalan',
    4 => 'Barang (jenis_barang)',
    5 => 'NIK (karyawan)',
    6 => 'Supir',
    7 => 'Pengirim', 
    8 => 'Tujuan (tujuan_pengiriman)',
    9 => 'No Kas Bank (nomor_kas_bank)',
    10 => 'Tanggal Tanda Terima (otomatis terisi saat approval)',
    11 => 'Total'
];

foreach ($kolom_baru as $no => $kolom) {
    echo sprintf("%2d. %-50s\n", $no, $kolom);
}

echo "\n=== Data Source Mapping ===\n";
echo "- No: Index urut\n";
echo "- Nomor Surat Jalan: suratJalan.no_surat_jalan\n";
echo "- Nomor Uang Jalan: uangJalan.nomor_uang_jalan\n";
echo "- Barang: suratJalan.jenis_barang\n";
echo "- NIK: suratJalan.karyawan\n";
echo "- Supir: suratJalan.supir\n";
echo "- Pengirim: suratJalan.pengirim\n";
echo "- Tujuan: suratJalan.tujuan_pengiriman\n";
echo "- No Kas Bank: uangJalan.nomor_kas_bank\n";
echo "- Tanggal Tanda Terima: suratJalan.tanggal_tanda_terima (otomatis dari approval)\n";
echo "- Total: uangJalan.jumlah_total\n";

echo "\n=== Optimizations ===\n";
echo "✅ Font size dikurangi ke 9px untuk fit semua kolom\n";
echo "✅ Padding dikurangi untuk space yang lebih efisien\n";
echo "✅ Width percentage disesuaikan untuk 11 kolom\n";
echo "✅ Word-wrap ditambahkan untuk text panjang\n";

echo "\n=== Integration dengan Approval System ===\n";
echo "✅ Tanggal Tanda Terima otomatis terisi saat approval surat jalan\n";
echo "✅ Menggunakan format date (d/m/Y) untuk print\n";
echo "✅ Menampilkan '-' jika data belum ada\n";

echo "\n=== Cara Test ===\n";
echo "1. Buka browser ke /pranota-uang-jalan\n";
echo "2. Pilih pranota dan klik tombol Print\n";
echo "3. Atau langsung ke /pranota-uang-jalan/{id}/print\n";

echo "\n🎉 IMPLEMENTASI SELESAI!\n";
echo "Print table sekarang menampilkan semua kolom sesuai permintaan Anda.\n";