<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LangsirBatam;

$startDate = '2026-07-25';
$endDate = '2026-08-10';
$supirs = [
    'DJUNAEDY', 
    'FEBRY', 
    'HENDRIADI', 
    'RIDWAN', 
    'MAMAN', 
    'TAUFIK H'
];

$query = LangsirBatam::withTrashed()
    ->whereBetween('tanggal', [$startDate, $endDate])
    ->whereIn('supir', $supirs);

$langsirs = $query->get();
$count = $langsirs->count();
echo "Ditemukan {$count} data langsir dari tanggal {$startDate} sampai {$endDate} untuk supir yang dipilih.\n";

if ($count > 0) {
    $deletedHistory = 0;
    
    foreach ($langsirs as $langsir) {
        // Menghapus HistoryKontainer yang berkaitan
        $deletedHistory += \App\Models\HistoryKontainer::where('keterangan', 'like', "%[No Transaksi: {$langsir->no_transaksi}]%")->forceDelete();
    }
    
    // Menghapus data permanen
    $deleted = $query->forceDelete();
    
    echo "Berhasil menghapus permanen {$deleted} data langsir.\n";
    echo "Berhasil menghapus permanen {$deletedHistory} data history/pergerakan kontainer.\n";
} else {
    echo "Tidak ada data yang perlu dihapus.\n";
}
