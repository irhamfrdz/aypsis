<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Checking pranota-perbaikan-kontainer permissions in database...\n\n";

$pranotaPermissions = Permission::where('name', 'like', 'pranota-perbaikan-kontainer-%')->get();
echo "Found " . $pranotaPermissions->count() . " pranota-perbaikan-kontainer permissions:\n";
foreach ($pranotaPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nChecking pembayaran-pranota-perbaikan-kontainer permissions...\n";
$pembayaranPermissions = Permission::where('name', 'like', 'pembayaran-pranota-perbaikan-kontainer-%')->get();
echo "Found " . $pembayaranPermissions->count() . " pembayaran-pranota-perbaikan-kontainer permissions:\n";
foreach ($pembayaranPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nTest completed.\n";
