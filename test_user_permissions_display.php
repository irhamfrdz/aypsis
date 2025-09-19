<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Use reflection to access private methods
$reflectionClass = new ReflectionClass(new UserController());
$convertPermissionsMethod = $reflectionClass->getMethod('convertPermissionsToMatrix');
$convertPermissionsMethod->setAccessible(true);

echo "Testing master-tipe-akun permission display for existing users...\n";

// Find a user that has master-tipe-akun permissions
$user = User::with('permissions')->first();

if (!$user) {
    echo "❌ No users found in database!\n";
    exit(1);
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n";

// Get user's current permissions
$userPermissions = $user->permissions()->pluck('name')->toArray();
echo "User has " . count($userPermissions) . " permissions\n";

// Check if user has master-tipe-akun permissions
$masterTipeAkunPermissions = array_filter($userPermissions, function($perm) {
    return strpos($perm, 'master-tipe-akun') === 0;
});

echo "User has " . count($masterTipeAkunPermissions) . " master-tipe-akun permissions: " . implode(', ', $masterTipeAkunPermissions) . "\n";

// Test convertPermissionsToMatrix with user's permissions
$matrixPermissions = $convertPermissionsMethod->invoke(new UserController(), $userPermissions);

echo "\nConverted to matrix format:\n";
echo json_encode($matrixPermissions, JSON_PRETTY_PRINT) . "\n";

// Check if master-tipe-akun is in the matrix
if (isset($matrixPermissions['master-tipe-akun'])) {
    echo "\n✅ SUCCESS: master-tipe-akun found in matrix!\n";
    echo "Actions: " . implode(', ', array_keys($matrixPermissions['master-tipe-akun'])) . "\n";

    // This means the checkboxes should be checked in the edit form
    foreach ($matrixPermissions['master-tipe-akun'] as $action => $enabled) {
        $status = $enabled ? 'checked' : 'unchecked';
        echo "  - {$action}: {$status}\n";
    }
} else {
    echo "\n❌ PROBLEM: master-tipe-akun NOT found in matrix!\n";
    echo "Available modules: " . implode(', ', array_keys($matrixPermissions)) . "\n";
}

echo "\nTest completed.\n";
