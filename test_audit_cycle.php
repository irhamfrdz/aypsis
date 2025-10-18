<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;

echo "=== TEST FULL CYCLE AUDIT LOG PERMISSIONS ===\n\n";

$admin = User::where('username', 'admin')->first();
$userController = new UserController();

echo "1️⃣ Original user permissions (before save):\n";
$originalPermissions = $admin->permissions->pluck('name')->toArray();
$auditOriginal = array_filter($originalPermissions, function($perm) {
    return strpos($perm, 'audit') !== false;
});
foreach ($auditOriginal as $perm) {
    echo "   ✅ {$perm}\n";
}

echo "\n2️⃣ Convert to matrix (like edit page load):\n";
$matrixPermissions = $userController->testConvertPermissionsToMatrix($originalPermissions);
if (isset($matrixPermissions['audit-log'])) {
    foreach ($matrixPermissions['audit-log'] as $action => $value) {
        echo "   audit-log[{$action}]: " . ($value ? 'checked' : 'unchecked') . "\n";
    }
} else {
    echo "   ❌ audit-log NOT found in matrix\n";
}

echo "\n3️⃣ Simulate form submission (matrix to IDs):\n";
$formSubmission = [
    'audit-log' => [
        'view' => true,
        'export' => true
    ]
];

$permissionIds = $userController->testConvertMatrixPermissionsToIds($formSubmission);
echo "   Permission IDs: " . implode(', ', $permissionIds) . "\n";

// Get permission names from IDs
use App\Models\Permission;
$permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
echo "   Permission names: " . implode(', ', $permissionNames) . "\n";

echo "\n4️⃣ Simulate sync and reload:\n";
// Sync permissions (simulate what happens in update method)
$admin->permissions()->sync($permissionIds);

// Reload user
$admin->refresh();
$newPermissions = $admin->permissions->pluck('name')->toArray();
$auditNew = array_filter($newPermissions, function($perm) {
    return strpos($perm, 'audit') !== false;
});

echo "   After sync:\n";
foreach ($auditNew as $perm) {
    echo "     ✅ {$perm}\n";
}

echo "\n5️⃣ Convert back to matrix (like edit page reload):\n";
$reloadMatrix = $userController->testConvertPermissionsToMatrix($newPermissions);
if (isset($reloadMatrix['audit-log'])) {
    foreach ($reloadMatrix['audit-log'] as $action => $value) {
        echo "   audit-log[{$action}]: " . ($value ? 'checked' : 'unchecked') . "\n";
    }
} else {
    echo "   ❌ audit-log NOT found in matrix after reload\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Full cycle test completed!\n";
