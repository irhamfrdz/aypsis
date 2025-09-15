<?php

// Test script to verify perbaikan-kontainer permission matrix conversion
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;
use Spatie\Permission\Models\Permission;
use ReflectionMethod;

echo "=== Testing Perbaikan Kontainer Permission Matrix Conversion ===\n";

// Create UserController instance
$controller = new UserController();

// Test permissions array (simulating permissions from database)
$testPermissions = [
    'perbaikan-kontainer.view',
    'perbaikan-kontainer.create',
    'perbaikan-kontainer.update',
    'perbaikan-kontainer.delete',
    'perbaikan-kontainer.print',
    'perbaikan-kontainer.export'
];

echo "Input permissions:\n";
foreach ($testPermissions as $perm) {
    echo "- $perm\n";
}

echo "\nConverting to matrix...\n";

// Use reflection to access private method
$reflectionMethod = new ReflectionMethod($controller, 'convertPermissionsToMatrix');
$reflectionMethod->setAccessible(true);
$matrix = $reflectionMethod->invoke($controller, $testPermissions);

echo "Matrix result:\n";
if (isset($matrix['perbaikan-kontainer'])) {
    echo "✅ perbaikan-kontainer module found in matrix:\n";
    foreach ($matrix['perbaikan-kontainer'] as $action => $value) {
        echo "  - $action: " . ($value ? 'true' : 'false') . "\n";
    }
} else {
    echo "❌ perbaikan-kontainer module NOT found in matrix\n";
    echo "Available modules in matrix:\n";
    foreach (array_keys($matrix) as $module) {
        echo "  - $module\n";
    }
}

echo "\nConverting back to permission IDs...\n";
$reflectionMethod2 = new ReflectionMethod($controller, 'convertMatrixPermissionsToIds');
$reflectionMethod2->setAccessible(true);
$permissionIds = $reflectionMethod2->invoke($controller, $matrix);

echo "Permission IDs found: " . count($permissionIds) . "\n";
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "- $perm->name (ID: $id)\n";
    }
}
