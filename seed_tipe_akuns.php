<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TipeAkun;

echo "Menambahkan data tipe akun standar...\n";
echo "=====================================\n";

$tipeAkuns = [
    ['tipe_akun' => 'Aktiva Lancar', 'catatan' => 'Aset yang dapat dicairkan dalam waktu 1 tahun'],
    ['tipe_akun' => 'Aktiva Tetap', 'catatan' => 'Aset yang digunakan dalam operasi jangka panjang'],
    ['tipe_akun' => 'Utang Lancar', 'catatan' => 'Kewajiban yang harus dibayar dalam waktu 1 tahun'],
    ['tipe_akun' => 'Utang Jangka Panjang', 'catatan' => 'Kewajiban yang jatuh tempo lebih dari 1 tahun'],
    ['tipe_akun' => 'Ekuitas', 'catatan' => 'Modal pemilik atau shareholder equity'],
    ['tipe_akun' => 'Pendapatan', 'catatan' => 'Penerimaan dari kegiatan operasional'],
    ['tipe_akun' => 'Beban', 'catatan' => 'Biaya yang dikeluarkan untuk operasional'],
    ['tipe_akun' => 'Pendapatan Lain', 'catatan' => 'Penerimaan di luar kegiatan operasional utama'],
    ['tipe_akun' => 'Beban Lain', 'catatan' => 'Biaya di luar kegiatan operasional utama'],
];

$added = 0;
$skipped = 0;

foreach ($tipeAkuns as $data) {
    // Cek apakah tipe akun sudah ada
    $existing = TipeAkun::where('tipe_akun', $data['tipe_akun'])->first();

    if (!$existing) {
        TipeAkun::create($data);
        echo "✅ Ditambahkan: {$data['tipe_akun']}\n";
        $added++;
    } else {
        echo "⏭️  Dilewati (sudah ada): {$data['tipe_akun']}\n";
        $skipped++;
    }
}

echo "\nRingkasan:\n";
echo "- Ditambahkan: $added\n";
echo "- Dilewati: $skipped\n";
echo "- Total sekarang: " . TipeAkun::count() . "\n";

echo "\nData tipe akun saat ini:\n";
$allTipeAkuns = TipeAkun::orderBy('tipe_akun')->get();
foreach ($allTipeAkuns as $tipeAkun) {
    echo "- {$tipeAkun->tipe_akun}";
    if ($tipeAkun->catatan) {
        echo " - {$tipeAkun->catatan}";
    }
    echo "\n";
}

echo "\nSelesai!\n";