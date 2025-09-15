<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== DEBUG: Testing convertMatrixPermissionsToIds for pranota-supir ===\n\n";

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Test data simulating form submission when pranota-supir-view is checked
$testMatrixData = [
    'permissions' => [
        'pranota' => [
            'supir-view' => '1'  // This simulates checking the pranota-supir view permission
        ]
    ]
];

echo "Test Matrix Data:\n";
print_r($testMatrixData);
echo "\n";

// Test the conversion method
$permissionIds = $method->invoke($controller, $testMatrixData);

echo "Converted Permission IDs:\n";
print_r($permissionIds);
echo "\n";

// Check what permissions these IDs correspond to
if (!empty($permissionIds)) {
    echo "Corresponding Permission Names:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- ID {$id}: {$permission->name}\n";
        } else {
            echo "- ID {$id}: NOT FOUND\n";
        }
    }
} else {
    echo "No permission IDs found!\n";
}

echo "\n=== Checking available pranota-supir permissions in database ===\n";

// Check what pranota-supir permissions exist in the database
$pranotaSupirPermissions = Permission::where('name', 'like', '%pranota-supir%')->get();

if ($pranotaSupirPermissions->count() > 0) {
    echo "Found pranota-supir permissions:\n";
    foreach ($pranotaSupirPermissions as $perm) {
        echo "- ID: {$perm->id}, Name: {$perm->name}\n";
    }
} else {
    echo "No pranota-supir permissions found in database!\n";
}

echo "\n=== Testing different permission name formats ===\n";

// Test different possible formats
$testFormats = [
    'pranota-supir-view',
    'pranota-supir.view',
    'pranota.supir-view',
    'pranota.supir.view',
    'pranota-supir',
    'pranota.supir'
];

foreach ($testFormats as $format) {
    $permission = Permission::where('name', $format)->first();
    if ($permission) {
        echo "✓ Found: {$format} (ID: {$permission->id})\n";
    } else {
        echo "✗ Not found: {$format}\n";
    }
}

echo "\n=== Testing user test4 current permissions ===\n";

// Check user test4 permissions
$user = User::where('username', 'test4')->first();
if ($user) {
    $userPermissions = $user->permissions->pluck('name')->toArray();
    echo "User test4 permissions:\n";
    print_r($userPermissions);

    $hasPranotaSupir = in_array('pranota-supir', $userPermissions) ||
                      in_array('pranota-supir-view', $userPermissions) ||
                      in_array('pranota-supir.view', $userPermissions);

    echo "\nHas pranota-supir permission: " . ($hasPranotaSupir ? 'YES' : 'NO') . "\n";
} else {
    echo "User test4 not found!\n";
}

?></content>
<parameter name="filePath">c:\folder_kerjaan\aypsis\debug_pranota_permission_conversion.php
