<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST USER APPROVAL PERMISSION MATRIX ===" . PHP_EOL;

// Test permission names yang baru ditambahkan
$userApprovalPermNames = [
    'user-approval',
    'user-approval-view',
    'user-approval-approve',
    'user-approval-reject',
    'user-approval-edit',
    'user-approval-history',
    'master-user-approve',
    'master-user-suspend',
    'master-user-activate'
];

echo "Input permission names:" . PHP_EOL;
foreach ($userApprovalPermNames as $name) {
    echo "  - {$name}" . PHP_EOL;
}

// Test convertPermissionsToMatrix function
$userController = new App\Http\Controllers\UserController();

try {
    $matrixPermissions = $userController->testConvertPermissionsToMatrix($userApprovalPermNames);
    
    echo PHP_EOL . "✅ Conversion successful!" . PHP_EOL;
    echo "Matrix permissions generated:" . PHP_EOL;
    print_r($matrixPermissions);
    
    // Test sebaliknya - matrix ke IDs
    echo PHP_EOL . "=== TEST MATRIX TO IDS ===" . PHP_EOL;
    $permissionIds = $userController->testConvertMatrixPermissionsToIds($matrixPermissions);
    
    echo "Permission IDs generated: " . implode(', ', $permissionIds) . PHP_EOL;
    
    // Get permission names for verification
    $permissions = App\Models\Permission::whereIn('id', $permissionIds)->pluck('name', 'id')->toArray();
    echo "Permission names found:" . PHP_EOL;
    foreach ($permissions as $id => $name) {
        echo "  - ID {$id}: {$name}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "=== TEST SELESAI ===" . PHP_EOL;