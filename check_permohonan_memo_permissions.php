<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Checking permohonan-memo permissions in database...\n\n";

$memoPermissions = Permission::where('name', 'like', 'permohonan-memo-%')->get();
echo "Found " . $memoPermissions->count() . " permohonan-memo permissions:\n";
foreach ($memoPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nChecking permohonan permissions...\n";
$permohonanPermissions = Permission::where('name', 'like', 'permohonan-%')->get();
echo "Found " . $permohonanPermissions->count() . " permohonan permissions:\n";
foreach ($permohonanPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nTest completed.\n";
