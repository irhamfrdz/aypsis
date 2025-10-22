<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;

echo "=== Testing Permission System ===\n\n";

$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit;
}

echo "✓ Admin user found: {$admin->username}\n";
echo "✓ User ID: {$admin->id}\n";

// Load permissions relationship
echo "\n=== Loading Permissions ===\n";
$adminWithPermissions = User::with('permissions')->find($admin->id);
$permissions = $adminWithPermissions->permissions;

echo "✓ Total permissions loaded: " . $permissions->count() . "\n";

// Check specific pergerakan kapal permissions
$pergerakanPermissions = $permissions->filter(function($perm) {
    return str_contains($perm->name, 'pergerakan-kapal');
});

echo "\n=== Pergerakan Kapal Permissions ===\n";
foreach($pergerakanPermissions as $perm) {
    echo "✓ {$perm->name}\n";
}

// Test the can() method
echo "\n=== Testing can() method ===\n";
$testPermissions = [
    'pergerakan-kapal.view',
    'pergerakan-kapal.create',
    'pergerakan-kapal.edit',
    'pergerakan-kapal.delete'
];

foreach($testPermissions as $testPerm) {
    $canAccess = $adminWithPermissions->can($testPerm);
    echo ($canAccess ? "✓" : "❌") . " Can access '{$testPerm}': " . ($canAccess ? "YES" : "NO") . "\n";
}

// Test hasPermissionTo method directly
echo "\n=== Testing hasPermissionTo() method ===\n";
foreach($testPermissions as $testPerm) {
    $hasPermission = $adminWithPermissions->hasPermissionTo($testPerm);
    echo ($hasPermission ? "✓" : "❌") . " Has permission '{$testPerm}': " . ($hasPermission ? "YES" : "NO") . "\n";
}

echo "\n=== Test Complete ===\n";
