<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Checking permissions by IDs [27,28,31,33,24]:\n";

$ids = [27,28,31,33,24];

foreach ($ids as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "ID $id: {$perm->name}\n";
    } else {
        echo "ID $id: Not found\n";
    }
}

echo "\nChecking what permissions actually exist for tagihan-kontainer-sewa:\n";

$perms = Permission::where('name', 'like', 'tagihan-kontainer-sewa.%')->get();

foreach ($perms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}
