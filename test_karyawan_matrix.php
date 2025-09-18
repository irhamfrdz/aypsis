<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;

// Create a test matrix with master-karyawan permissions
$testMatrix = [
    'master-karyawan' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

echo "Testing convertMatrixPermissionsToIds with master-karyawan:\n";
echo "Input matrix: " . json_encode($testMatrix, JSON_PRETTY_PRINT) . "\n\n";

// Create controller instance and test the method
$controller = new UserController();
$permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrix);

echo "Permission IDs returned: " . json_encode($permissionIds) . "\n\n";

// Get permission names for verification
$permissions = \App\Models\Permission::whereIn('id', $permissionIds)->get();
echo "Permission names:\n";
foreach ($permissions as $permission) {
    echo "- " . $permission->name . "\n";
}

echo "\nTest completed successfully!\n";
