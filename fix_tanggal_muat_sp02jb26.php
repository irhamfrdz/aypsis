<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cari data naik_kapal untuk voyage SP02JB26 yang tanggal muatnya bukan bulan Januari 2026
$rogueRecords = App\Models\NaikKapal::where('no_voyage', 'SP02JB26')
    ->whereMonth('tanggal_muat', '!=', 1) // Bukan bulan 1 (Januari)
    ->get();

$count = 0;
foreach ($rogueRecords as $record) {
    // Kita set kembali ke tanggal 23 Januari 2026 (sama seperti data awal yang benar)
    $record->update([
        'tanggal_muat' => '2026-01-23 17:00:00'
    ]);
    $count++;
}

echo "Berhasil memperbaiki " . $count . " data tanggal muat untuk voyage SP02JB26 menjadi Januari 2026.\n";
