<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\UserController;
use App\Models\Permission;

echo "Testing permission conversion methods for master-tipe-akun...\n";

// Test data - simulate form submission with master-tipe-akun permissions checked
$testMatrixPermissions = [
    'master-tipe-akun' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ]
];

$controller = new UserController();

// Use reflection to access private methods
$reflectionClass = new ReflectionClass($controller);
$convertMatrixMethod = $reflectionClass->getMethod('convertMatrixPermissionsToIds');
$convertMatrixMethod->setAccessible(true);

$convertPermissionsMethod = $reflectionClass->getMethod('convertPermissionsToMatrix');
$convertPermissionsMethod->setAccessible(true);

try {
    // Test convertMatrixPermissionsToIds
    echo "\n=== Testing convertMatrixPermissionsToIds ===\n";
    $permissionIds = $convertMatrixMethod->invoke($controller, $testMatrixPermissions);

    echo "Matrix permissions: " . json_encode($testMatrixPermissions, JSON_PRETTY_PRINT) . "\n";
    echo "Permission IDs found: " . count($permissionIds) . "\n";

    if (count($permissionIds) > 0) {
        echo "Permission IDs: " . implode(', ', $permissionIds) . "\n";

        // Get permission names for verification
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        echo "Permission names: " . implode(', ', $permissionNames) . "\n";
    } else {
        echo "❌ No permission IDs found!\n";
    }

    // Test convertPermissionsToMatrix with the found permission NAMES (not IDs)
    echo "\n=== Testing convertPermissionsToMatrix ===\n";
    echo "Permission names being converted: " . implode(', ', $permissionNames) . "\n";

    $matrixPermissions = $convertPermissionsMethod->invoke($controller, $permissionNames);

    echo "Converted back to matrix: " . json_encode($matrixPermissions, JSON_PRETTY_PRINT) . "\n";

    // Check if master-tipe-akun permissions are properly converted
    if (isset($matrixPermissions['master-tipe-akun'])) {
        echo "✅ master-tipe-akun permissions found in matrix!\n";
        echo "Actions: " . implode(', ', array_keys($matrixPermissions['master-tipe-akun'])) . "\n";
    } else {
        echo "❌ master-tipe-akun permissions NOT found in matrix!\n";
        echo "Available modules: " . implode(', ', array_keys($matrixPermissions)) . "\n";
    }

    echo "\n✅ Test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
