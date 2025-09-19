<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tujuan;

echo "Testing kolom uang_jalan di tabel tujuans...\n";
echo "==============================================\n";

// Cek apakah ada data tujuan
$count = Tujuan::count();
echo "Jumlah data tujuan: $count\n\n";

if ($count > 0) {
    // Ambil satu data tujuan untuk test
    $tujuan = Tujuan::first();
    echo "Data tujuan pertama:\n";
    echo "- ID: {$tujuan->id}\n";
    echo "- Cabang: {$tujuan->cabang}\n";
    echo "- Wilayah: {$tujuan->wilayah}\n";
    echo "- Rute: {$tujuan->rute}\n";
    echo "- Uang Jalan: Rp " . number_format($tujuan->uang_jalan ?? 0, 0, ',', '.') . "\n";
    echo "- Uang Jalan 20ft: Rp " . number_format($tujuan->uang_jalan_20 ?? 0, 0, ',', '.') . "\n";
    echo "- Uang Jalan 40ft: Rp " . number_format($tujuan->uang_jalan_40 ?? 0, 0, ',', '.') . "\n\n";

    // Test update uang_jalan
    echo "Testing update kolom uang_jalan...\n";
    $oldValue = $tujuan->uang_jalan;
    $newValue = 150000;

    $tujuan->uang_jalan = $newValue;
    $tujuan->save();

    // Refresh dari database
    $tujuan->refresh();

    if ($tujuan->uang_jalan == $newValue) {
        echo "✅ Update berhasil! Uang jalan: Rp " . number_format($tujuan->uang_jalan, 0, ',', '.') . "\n";

        // Kembalikan ke nilai lama
        $tujuan->uang_jalan = $oldValue;
        $tujuan->save();
        echo "✅ Data dikembalikan ke nilai semula.\n";
    } else {
        echo "❌ Update gagal!\n";
    }

} else {
    echo "Tidak ada data tujuan. Membuat data test...\n";

    // Buat data test
    $testData = [
        'cabang' => 'JKT',
        'wilayah' => 'Jakarta',
        'rute' => 'Jakarta - Bandung',
        'uang_jalan' => 100000,
        'uang_jalan_20' => 50000,
        'ongkos_truk_20' => 25000,
        'uang_jalan_40' => 75000,
        'ongkos_truk_40' => 35000,
        'antar_20' => 20000,
        'antar_40' => 30000,
    ];

    $tujuan = Tujuan::create($testData);

    if ($tujuan) {
        echo "✅ Data test berhasil dibuat!\n";
        echo "- ID: {$tujuan->id}\n";
        echo "- Uang Jalan: Rp " . number_format($tujuan->uang_jalan, 0, ',', '.') . "\n";
    } else {
        echo "❌ Gagal membuat data test!\n";
    }
}

echo "\nTest selesai!\n";