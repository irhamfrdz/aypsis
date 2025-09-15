<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== TESTING WITH PUBLIC METHOD ===\n\n";

// Create UserController instance
$controller = new UserController();

// Test data with correct format
$testMatrixData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1'
        ]
    ]
];

echo "Test Data:\n";
print_r($testMatrixData);
echo "\n";

// Use the public test method
$permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrixData);

echo "Result from public method:\n";
print_r($permissionIds);
echo "\n";

if (!empty($permissionIds)) {
    echo "SUCCESS! Found permissions:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- ID {$id}: {$permission->name}\n";
        }
    }
} else {
    echo "❌ FAILED: No permissions found\n";
}

?>
