<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "🧪 Testing convertMatrixPermissionsToIds with master-karyawan main permission\n\n";

// Create a UserController instance
$controller = new UserController();

// Simulate form data with master-karyawan permissions including main
$formData = [
    'master-karyawan' => [
        'view' => '1',
        'create' => '1',
        'main' => '1'  // This should now be handled correctly
    ]
];

echo "📝 Form data:\n";
print_r($formData);
echo "\n";

// Use reflection to access the private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Call the method
$permissionIds = $method->invoke($controller, $formData);

echo "🔍 Permission IDs returned: " . implode(', ', $permissionIds) . "\n\n";

// Get permission details
$permissions = Permission::whereIn('id', $permissionIds)->get();
echo "📋 Permissions found:\n";
foreach ($permissions as $perm) {
    echo "  - ID: {$perm->id}, Name: {$perm->name}\n";
}

echo "\n✅ Test completed!\n";
