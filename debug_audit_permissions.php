<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== DEBUG AUDIT LOG PERMISSIONS ===\n\n";

// Test user admin
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit(1);
}

echo "👤 Testing user: {$admin->username}\n";

// Check existing permissions
echo "\n🔍 Current permissions:\n";
$currentPermissions = $admin->permissions()->pluck('name')->toArray();
$auditPermissions = array_filter($currentPermissions, function($perm) {
    return strpos($perm, 'audit') !== false;
});

foreach ($auditPermissions as $perm) {
    echo "   ✅ {$perm}\n";
}

if (empty($auditPermissions)) {
    echo "   ❌ Tidak ada permission audit ditemukan\n";
}

// Test convertPermissionsToMatrix method
echo "\n🔄 Testing convertPermissionsToMatrix:\n";

// Import the controller to test the method
use App\Http\Controllers\UserController;

$userController = new UserController();

// Test conversion
$testPermissions = ['audit-log-view', 'audit-log-export'];
echo "Input permissions: " . implode(', ', $testPermissions) . "\n";

try {
    $matrixResult = $userController->testConvertPermissionsToMatrix($testPermissions);
    echo "Matrix result:\n";
    var_dump($matrixResult);
} catch (Exception $e) {
    echo "❌ Error in convertPermissionsToMatrix: " . $e->getMessage() . "\n";
}

// Test convertMatrixPermissionsToIds method
echo "\n🔄 Testing convertMatrixPermissionsToIds:\n";

$testMatrix = [
    'audit-log' => [
        'view' => true,
        'export' => true
    ]
];

echo "Input matrix:\n";
var_dump($testMatrix);

try {
    $idsResult = $userController->testConvertMatrixPermissionsToIds($testMatrix);
    echo "IDs result: " . implode(', ', $idsResult) . "\n";

    // Convert IDs back to permission names for verification
    $permissions = Permission::whereIn('id', $idsResult)->pluck('name')->toArray();
    echo "Permission names: " . implode(', ', $permissions) . "\n";
} catch (Exception $e) {
    echo "❌ Error in convertMatrixPermissionsToIds: " . $e->getMessage() . "\n";
}

// Test actual edit page conversion
echo "\n🔄 Testing edit page conversion:\n";
$userSimplePermissions = $admin->permissions->pluck('name')->toArray();
$userMatrixPermissions = $userController->testConvertPermissionsToMatrix($userSimplePermissions);

if (isset($userMatrixPermissions['audit-log'])) {
    echo "✅ Audit log found in matrix:\n";
    foreach ($userMatrixPermissions['audit-log'] as $action => $value) {
        echo "   {$action}: " . ($value ? 'true' : 'false') . "\n";
    }
} else {
    echo "❌ Audit log NOT found in matrix\n";
    echo "Available modules in matrix: " . implode(', ', array_keys($userMatrixPermissions)) . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Debug selesai!\n";
