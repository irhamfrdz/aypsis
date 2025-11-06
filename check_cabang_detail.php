<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;

echo "=== DETAIL CABANG KARYAWAN ===\n\n";

// Cek semua nilai cabang yang ada
$allKaryawan = Karyawan::all();
echo "Total karyawan: {$allKaryawan->count()}\n\n";

echo "Semua nilai cabang yang ada:\n";
foreach ($allKaryawan as $karyawan) {
    echo "- ID: {$karyawan->id}, Nama: {$karyawan->nama_lengkap}, Cabang: '{$karyawan->cabang}'\n";
}

echo "\n=== RINGKASAN CABANG ===\n";
$cabangCounts = Karyawan::selectRaw('cabang, COUNT(*) as count')
    ->groupBy('cabang')
    ->get();

foreach ($cabangCounts as $cabang) {
    echo "Cabang '{$cabang->cabang}': {$cabang->count} karyawan\n";
}

// Cek apakah ada cabang yang mirip BTM
echo "\n=== PENCARIAN CABANG MIRIP BTM ===\n";
$similarToBTM = Karyawan::where('cabang', 'LIKE', '%BTM%')
    ->orWhere('cabang', 'LIKE', '%btm%')
    ->orWhere('cabang', 'LIKE', '%Btm%')
    ->get();

if ($similarToBTM->count() > 0) {
    echo "Ditemukan cabang mirip BTM:\n";
    foreach ($similarToBTM as $karyawan) {
        echo "- {$karyawan->nama_lengkap}: '{$karyawan->cabang}'\n";
    }
} else {
    echo "Tidak ada cabang yang mirip dengan 'BTM'\n";
}

echo "\n=== END DETAIL ===\n";