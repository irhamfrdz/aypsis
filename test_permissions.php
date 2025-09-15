<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Permission Database\n";
echo "==========================\n";

// Test basic permission lookup
$testNames = ['dashboard-view', 'master-karyawan', 'master.karyawan.index', 'master-user-view'];

echo "Looking for permissions: " . implode(', ', $testNames) . "\n\n";

foreach ($testNames as $name) {
    $permission = Permission::where('name', $name)->first();
    if ($permission) {
        echo "✓ Found: $name (ID: {$permission->id})\n";
    } else {
        echo "✗ Not found: $name\n";
    }
}

echo "\nTotal permissions in database: " . Permission::count() . "\n";

// Show some sample permissions
echo "\nSample permissions from database:\n";
$samplePermissions = Permission::take(10)->get();
foreach ($samplePermissions as $perm) {
    echo "  {$perm->id}: {$perm->name}\n";
}
