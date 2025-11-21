<?php

/**
 * Script to check pembayaran-pranota-uang-jalan permissions in database
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "=== Checking Pembayaran Pranota Uang Jalan Permissions ===\n\n";

$permissionNames = [
    'pembayaran-pranota-uang-jalan-view',
    'pembayaran-pranota-uang-jalan-create',
    'pembayaran-pranota-uang-jalan-edit',
    'pembayaran-pranota-uang-jalan-delete',
    'pembayaran-pranota-uang-jalan-approve',
    'pembayaran-pranota-uang-jalan-print',
    'pembayaran-pranota-uang-jalan-export'
];

$found = [];
$missing = [];

foreach ($permissionNames as $permName) {
    $permission = Permission::where('name', $permName)->first();
    
    if ($permission) {
        $found[] = [
            'name' => $permName,
            'id' => $permission->id,
            'description' => $permission->description
        ];
        echo "✓ FOUND: {$permName} (ID: {$permission->id})\n";
    } else {
        $missing[] = $permName;
        echo "✗ MISSING: {$permName}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Found: " . count($found) . " permissions\n";
echo "Missing: " . count($missing) . " permissions\n";

if (count($missing) > 0) {
    echo "\n=== Creating Missing Permissions ===\n";
    
    foreach ($missing as $permName) {
        $description = str_replace('-', ' ', ucwords($permName, '-'));
        $permission = Permission::create([
            'name' => $permName,
            'description' => $description
        ]);
        echo "✓ Created: {$permName} (ID: {$permission->id})\n";
    }
    
    echo "\nAll missing permissions have been created!\n";
}

echo "\nDone!\n";
