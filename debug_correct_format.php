<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== TESTING WITH CORRECT FORM FORMAT ===\n\n";

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Test with the CORRECT format from the actual form
$correctFormatData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1'
        ]
    ]
];

echo "Correct Form Format Data (from actual HTML form):\n";
print_r($correctFormatData);
echo "\n";

$permissionIds = $method->invoke($controller, $correctFormatData);

echo "Converted Permission IDs:\n";
print_r($permissionIds);
echo "\n";

if (!empty($permissionIds)) {
    echo "SUCCESS! Found permissions:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- ID {$id}: {$permission->name}\n";
        } else {
            echo "- ID {$id}: NOT FOUND\n";
        }
    }
} else {
    echo "❌ Still no permissions found - there's another issue\n";
}

// Let's also test multiple permissions
$multiplePermissionsData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1',
            'create' => '1',
            'update' => '1'
        ]
    ]
];

echo "\n=== Testing Multiple Permissions ===\n";
echo "Data: " . print_r($multiplePermissionsData, true);

$permissionIds2 = $method->invoke($controller, $multiplePermissionsData);
echo "Result: " . print_r($permissionIds2, true);

if (!empty($permissionIds2)) {
    echo "Multiple permissions found:\n";
    foreach ($permissionIds2 as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- {$permission->name} (ID: {$id})\n";
        }
    }
}

?>
