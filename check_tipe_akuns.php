<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TipeAkun;

echo "Memeriksa data tipe_akuns...\n";
echo "================================\n";

$count = TipeAkun::count();
echo "Jumlah data tipe_akuns: $count\n\n";

if ($count > 0) {
    echo "Data tipe_akuns:\n";
    $tipeAkuns = TipeAkun::orderBy('tipe_akun')->get();
    foreach ($tipeAkuns as $tipeAkun) {
        echo "- {$tipeAkun->tipe_akun}";
        if ($tipeAkun->catatan) {
            echo " - {$tipeAkun->catatan}";
        }
        echo "\n";
    }
} else {
    echo "❌ Tidak ada data di tabel tipe_akuns!\n";
    echo "Anda perlu menambahkan data tipe akun terlebih dahulu.\n";
}

echo "\nSelesai.\n";
