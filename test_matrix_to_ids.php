<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing matrix to IDs conversion\n";
echo "================================\n";

// Test matrix for tagihan-kontainer
$testMatrix = [
    'tagihan-kontainer' => [
        'view' => '1',
        'create' => '1',
        'update' => '1'
    ]
];

$userController = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

$idsResult = $method->invoke($userController, $testMatrix);

echo "Input matrix:\n";
print_r($testMatrix);
echo "\nPermission IDs found: " . count($idsResult) . "\n";
print_r($idsResult);

// Show which permissions were found
if (!empty($idsResult)) {
    $foundPermissions = Permission::whereIn('id', $idsResult)->pluck('name', 'id');
    echo "\nFound permissions:\n";
    foreach ($foundPermissions as $id => $name) {
        echo "  ID $id: $name\n";
    }
} else {
    echo "\nNo permissions found!\n";
}

// Test individual permission lookups
echo "\nTesting individual permission lookups:\n";
$testPermissions = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'tagihan-kontainer-update'
];

foreach ($testPermissions as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if ($perm) {
        echo "✓ $permName found (ID: {$perm->id})\n";
    } else {
        echo "✗ $permName NOT found\n";
    }
}
