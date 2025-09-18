<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;

// Get a test user
$user = User::first();
if (!$user) {
    echo "No users found in database\n";
    exit;
}

echo "Testing user update with master-karyawan permissions...\n";
echo "User: {$user->username} (ID: {$user->id})\n\n";

// Simulate form data with master-karyawan permissions
$formData = [
    'username' => $user->username,
    'karyawan_id' => $user->karyawan_id,
    'permissions' => [
        'master-karyawan' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ]
    ]
];

echo "Form data permissions: " . json_encode($formData['permissions'], JSON_PRETTY_PRINT) . "\n\n";

// Create controller and test the conversion
$controller = new UserController();
$permissionIds = $controller->testConvertMatrixPermissionsToIds($formData['permissions']);

echo "Converted permission IDs: " . json_encode($permissionIds) . "\n\n";

// Get permission names
$permissions = \App\Models\Permission::whereIn('id', $permissionIds)->get();
echo "Permission names that would be assigned:\n";
foreach ($permissions as $permission) {
    echo "- {$permission->name} (ID: {$permission->id})\n";
}

echo "\nCurrent user permissions before update:\n";
$currentPermissions = $user->permissions->pluck('name', 'id');
foreach ($currentPermissions as $id => $name) {
    echo "- {$name} (ID: {$id})\n";
}

echo "\nTest completed!\n";
