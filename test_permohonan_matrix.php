<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;

// Create a test matrix data for permohonan
$testMatrixData = [
    'permohonan' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'approve' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

echo '=== TESTING convertMatrixPermissionsToIds FOR PERMOHONAN ===' . PHP_EOL;
echo 'Input matrix data:' . PHP_EOL;
print_r($testMatrixData);

echo PHP_EOL;

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Call the method
$result = $method->invoke($controller, $testMatrixData);

echo 'Permission IDs returned:' . PHP_EOL;
print_r($result);

echo PHP_EOL;
echo 'Permission names:' . PHP_EOL;
foreach ($result as $id) {
    $permission = \App\Models\Permission::find($id);
    if ($permission) {
        echo '- ' . $permission->name . PHP_EOL;
    }
}
