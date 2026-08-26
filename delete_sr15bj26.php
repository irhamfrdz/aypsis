<?php

// Pastikan script dijalankan dari command line (CLI)
if (php_sapi_name() !== 'cli') {
    die("Script ini hanya boleh dijalankan dari terminal/command line.\n");
}

echo "Memulai proses penghapusan...\n";

// Bootstrap framework Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Inisiasi Kernel Console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Cek jumlah data yang akan dihapus
    $count = \App\Models\Manifest::where('no_voyage', 'SR15BJ26')->count();

    if ($count > 0) {
        // Eksekusi hapus
        \App\Models\Manifest::where('no_voyage', 'SR15BJ26')->delete();
        echo "SUKSES: Berhasil menghapus {$count} data manifest dengan voyage SR15BJ26!\n";
    } else {
        echo "INFO: Tidak ada data manifest yang ditemukan dengan voyage SR15BJ26.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: Terjadi kesalahan saat menghapus data:\n";
    echo $e->getMessage() . "\n";
}
