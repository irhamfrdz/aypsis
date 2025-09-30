<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== SEMUA PERMISSION TAGIHAN-KONTAINER-SEWA ===\n";
$perms = Permission::where('name', 'like', '%tagihan-kontainer-sewa%')->get();
foreach ($perms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}

echo "\n=== CHECK DOT NOTATION ===\n";
$dotPerms = Permission::whereIn('name', [
    'tagihan-kontainer-sewa.index',
    'tagihan-kontainer-sewa.create',
    'tagihan-kontainer-sewa.update',
    'tagihan-kontainer-sewa.destroy',
    'tagihan-kontainer-sewa.export'
])->get();
echo "Dot notation permissions found: " . $dotPerms->count() . "\n";

echo "\n=== CHECK DASH NOTATION ===\n";
$dashPerms = Permission::whereIn('name', [
    'tagihan-kontainer-sewa-index',
    'tagihan-kontainer-sewa-create',
    'tagihan-kontainer-sewa-update',
    'tagihan-kontainer-sewa-destroy',
    'tagihan-kontainer-sewa-export'
])->get();
echo "Dash notation permissions found: " . $dashPerms->count() . "\n";
foreach ($dashPerms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}

echo "\n=== CHECK APAKAH ADA PERMISSION DENGAN DOT LAINNYA ===\n";
$allDotPerms = Permission::where('name', 'like', '%.%')->where('name', 'like', '%tagihan-kontainer-sewa%')->get();
echo "Dot notation variants found: " . $allDotPerms->count() . "\n";
foreach ($allDotPerms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}
