<?php

// script untuk dijalankan di root project laravel (misal di server production)

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PranotaStock;

echo "Memulai update lokasi pranota...\n";

// Update semua data pranota yang lokasinya masih kosong (null) menjadi 'Jakarta'
$updated = PranotaStock::whereNull('lokasi')->orWhere('lokasi', '')->update([
    'lokasi' => 'Jakarta'
]);

echo "Berhasil mengupdate {$updated} data pranota menjadi lokasi Jakarta.\n";
