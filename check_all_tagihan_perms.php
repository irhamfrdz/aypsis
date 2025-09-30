<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "All permissions containing 'tagihan-kontainer-sewa':\n";

$perms = Permission::where('name', 'like', '%tagihan-kontainer-sewa%')->get();

foreach ($perms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}

echo "\nChecking if CompletePermissionSeeder was run:\n";

$seederPerms = Permission::whereIn('name', [
    'tagihan-kontainer-sewa.index',
    'tagihan-kontainer-sewa.create',
    'tagihan-kontainer-sewa.update',
    'tagihan-kontainer-sewa.destroy',
    'tagihan-kontainer-sewa.export'
])->get();

echo "Dot notation permissions found: " . $seederPerms->count() . "\n";

foreach ($seederPerms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}
