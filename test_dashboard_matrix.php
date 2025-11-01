<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST DASHBOARD PERMISSION MATRIX ===" . PHP_EOL;

// Simulate matrix permissions input (seperti yang dikirim dari form)
$matrixPermissions = [
    'dashboard' => [
        'view' => '1'  // Checkbox checked
    ]
];

echo "Input matrix permissions:" . PHP_EOL;
print_r($matrixPermissions);

// Test convertMatrixPermissionsToIds function
$userController = new App\Http\Controllers\UserController();

try {
    $permissionIds = $userController->testConvertMatrixPermissionsToIds($matrixPermissions);
    
    echo "✅ Conversion successful!" . PHP_EOL;
    echo "Permission IDs generated: " . implode(', ', $permissionIds) . PHP_EOL;
    
    // Get permission names for verification
    $permissions = App\Models\Permission::whereIn('id', $permissionIds)->pluck('name', 'id')->toArray();
    echo "Permission names:" . PHP_EOL;
    foreach ($permissions as $id => $name) {
        echo "  - ID {$id}: {$name}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "=== TEST SELESAI ===" . PHP_EOL;