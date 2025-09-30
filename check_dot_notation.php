<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== CEK TAGIHAN KONTAINER SEWA DENGAN DOT NOTATION ===\n";
$dotPerms = Permission::where('name', 'like', 'tagihan-kontainer-sewa.%')->get();
echo "Permission dengan pola 'tagihan-kontainer-sewa.*': " . $dotPerms->count() . "\n";
foreach ($dotPerms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}

echo "\n=== CEK SEMUA PERMISSION YANG MENGANDUNG 'tagihan-kontainer-sewa' DAN MENGGUNAKAN DOT ===\n";
$allDotTagihan = Permission::where('name', 'like', '%tagihan-kontainer-sewa%')
    ->where('name', 'like', '%.%')
    ->get();
echo "Permission dengan 'tagihan-kontainer-sewa' dan dot: " . $allDotTagihan->count() . "\n";
foreach ($allDotTagihan as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}

echo "\n=== CEK DAFTAR TAGIHAN KONTAINER SEWA (MODULE LAIN) ===\n";
$daftarPerms = Permission::where('name', 'like', 'daftar-tagihan-kontainer-sewa.%')->get();
echo "Permission dengan pola 'daftar-tagihan-kontainer-sewa.*': " . $daftarPerms->count() . "\n";
foreach ($daftarPerms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}
