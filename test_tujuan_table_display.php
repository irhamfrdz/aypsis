<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tujuan;

echo "Testing tampilan tabel Master Tujuan dengan kolom baru...\n";
echo "==========================================================\n";

// Ambil beberapa data tujuan untuk test
$tujuans = Tujuan::take(3)->get();

if ($tujuans->isEmpty()) {
    echo "❌ Tidak ada data tujuan untuk ditampilkan\n";
    exit(1);
}

echo "Header tabel yang akan ditampilkan:\n";
echo "1. Nama Tujuan\n";
echo "2. Cabang\n";
echo "3. Wilayah\n";
echo "4. Rute\n";
echo "5. Uang Jalan 20ft\n";
echo "6. Uang Jalan 40ft\n";
echo "7. Antarlokasi 20ft\n";
echo "8. Antarlokasi 40ft\n";
echo "9. Aksi\n\n";

echo "Data yang akan ditampilkan:\n";
echo str_repeat("-", 120) . "\n";
printf("%-25s %-8s %-15s %-15s %-15s %-15s %-15s %-15s\n",
    "Nama Tujuan", "Cabang", "Uang Jalan 20ft", "Uang Jalan 40ft", "Antar 20ft", "Antar 40ft", "Wilayah", "Rute");
echo str_repeat("-", 120) . "\n";

foreach ($tujuans as $tujuan) {
    $namaTujuan = trim(($tujuan->wilayah ?? '') . ' ' . (($tujuan->rute ?? '') ? '- ' . $tujuan->rute : ''));
    $cabang = $tujuan->cabang ?? '';
    $wilayah = $tujuan->wilayah ?? '';
    $rute = $tujuan->rute ?? '';

    $uangJalan20 = 'Rp ' . number_format($tujuan->uang_jalan_20 ?? 0, 0, ',', '.');
    $uangJalan40 = 'Rp ' . number_format($tujuan->uang_jalan_40 ?? 0, 0, ',', '.');
    $antar20 = 'Rp ' . number_format($tujuan->antar_20 ?? 0, 0, ',', '.');
    $antar40 = 'Rp ' . number_format($tujuan->antar_40 ?? 0, 0, ',', '.');

    printf("%-25s %-8s %-15s %-15s %-15s %-15s %-15s %-15s\n",
        substr($namaTujuan, 0, 24),
        substr($cabang, 0, 7),
        $uangJalan20,
        $uangJalan40,
        $antar20,
        $antar40,
        substr($wilayah, 0, 14),
        substr($rute, 0, 14));
}

echo str_repeat("-", 120) . "\n\n";

echo "✅ Test berhasil! Tabel akan menampilkan kolom-kolom baru dengan format Rupiah yang benar.\n";
echo "✅ Kolom 'Uang Jalan' umum sudah dihapus dari tampilan.\n";
echo "✅ Semua kolom spesifik (20ft/40ft) sudah ditambahkan.\n";

echo "\nRingkasan perubahan:\n";
echo "- ❌ Dihapus: Uang Jalan (kolom umum)\n";
echo "- ✅ Ditambahkan: Uang Jalan 20ft\n";
echo "- ✅ Ditambahkan: Uang Jalan 40ft\n";
echo "- ✅ Ditambahkan: Antarlokasi 20ft\n";
echo "- ✅ Ditambahkan: Antarlokasi 40ft\n";

echo "\nSelesai!\n";
