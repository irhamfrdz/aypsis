<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "Testing Pranota Kontainer Sewa Permissions...\n\n";

// Get current user (adjust username if needed)
$username = 'admin'; // Change this to your username
$user = User::where('username', $username)->first();

if (!$user) {
    die("User '{$username}' not found. Please change the username in the script.\n");
}

echo "User: {$user->username}\n";
echo "User ID: {$user->id}\n\n";

// Check pranota-kontainer-sewa permissions
$pranotaPermissions = Permission::where('name', 'LIKE', 'pranota-kontainer-sewa-%')->get();

echo "Available pranota-kontainer-sewa permissions in database:\n";
foreach ($pranotaPermissions as $perm) {
    $hasPermission = $user->permissions()->where('permission_id', $perm->id)->exists();
    $status = $hasPermission ? '✓ HAS' : '✗ NO';
    echo "  {$status} - {$perm->name}\n";
}

echo "\n";

// Check if user can access pranota-kontainer-sewa
$requiredPermission = 'pranota-kontainer-sewa-view';
$hasRequiredPermission = $user->permissions()->where('name', $requiredPermission)->exists();

echo "Can access pranota-kontainer-sewa.index route?\n";
echo "  Required permission: {$requiredPermission}\n";
echo "  Has permission: " . ($hasRequiredPermission ? 'YES ✓' : 'NO ✗') . "\n\n";

// Show all user's pranota-related permissions
echo "All user's pranota-related permissions:\n";
$userPranotaPerms = $user->permissions()->where('name', 'LIKE', '%pranota%')->get();
foreach ($userPranotaPerms as $perm) {
    echo "  - {$perm->name}\n";
}

if ($userPranotaPerms->isEmpty()) {
    echo "  (No pranota permissions found)\n";
}

echo "\n";

// Test matrix permission conversion
echo "Testing matrix permission conversion...\n";
$controller = new \App\Http\Controllers\UserController();

// Test converting pranota-kontainer-sewa matrix permission to IDs
$testMatrixPermissions = [
    'pranota-kontainer-sewa' => [
        'view' => true,
        'create' => true,
        'print' => true
    ]
];

echo "Input matrix permissions:\n";
echo json_encode($testMatrixPermissions, JSON_PRETTY_PRINT) . "\n\n";

$permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrixPermissions);

echo "Converted to permission IDs: " . implode(', ', $permissionIds) . "\n";
echo "Permission names:\n";
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "  - {$perm->name}\n";
    }
}

echo "\n";

// Test reverse conversion (permission names to matrix)
$testPermissionNames = [
    'pranota-kontainer-sewa-view',
    'pranota-kontainer-sewa-create',
    'pranota-kontainer-sewa-print'
];

echo "Testing reverse conversion (names to matrix)...\n";
echo "Input permission names:\n";
foreach ($testPermissionNames as $name) {
    echo "  - {$name}\n";
}

$matrixResult = $controller->testConvertPermissionsToMatrix($testPermissionNames);

echo "\nConverted to matrix format:\n";
echo json_encode($matrixResult, JSON_PRETTY_PRINT) . "\n";
