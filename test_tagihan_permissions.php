<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Testing tagihan-kontainer-sewa permissions (dash notation):\n";

$permissions = [
    'tagihan-kontainer-sewa-index',
    'tagihan-kontainer-sewa-create',
    'tagihan-kontainer-sewa-update',
    'tagihan-kontainer-sewa-destroy',
    'tagihan-kontainer-sewa-export'
];

foreach ($permissions as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if ($perm) {
        echo "✓ Found: $permName (ID: {$perm->id})\n";
    } else {
        echo "✗ Missing: $permName\n";
    }
}

echo "\nTesting UserController conversion methods:\n";

$userController = new \App\Http\Controllers\UserController();

// Test matrix to IDs conversion
$testMatrix = [
    'tagihan-kontainer-sewa' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'export' => '1'
    ]
];

$ids = $userController->testConvertMatrixPermissionsToIds($testMatrix);
echo "Matrix to IDs result: " . json_encode($ids) . "\n";

// Test IDs to matrix conversion
$permissionNames = [
    'tagihan-kontainer-sewa-index',
    'tagihan-kontainer-sewa-create',
    'tagihan-kontainer-sewa-update',
    'tagihan-kontainer-sewa-destroy',
    'tagihan-kontainer-sewa-export'
];

$matrix = $userController->testConvertPermissionsToMatrix($permissionNames);
echo "Names to Matrix result: " . json_encode($matrix) . "\n";
