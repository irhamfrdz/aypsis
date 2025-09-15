<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;

// Simulate form data with permohonan view checked
$formData = [
    'permissions' => [
        'permohonan' => [
            'view' => '1'
        ]
    ]
];

echo '=== SIMULATING FORM SUBMISSION WITH PERMOHONAN VIEW ===' . PHP_EOL;
echo 'Form data:' . PHP_EOL;
print_r($formData);

echo PHP_EOL;

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Call the method
$permissionIds = $method->invoke($controller, $formData['permissions']);

echo 'Permission IDs that would be saved:' . PHP_EOL;
print_r($permissionIds);

echo PHP_EOL;
echo 'Permission names that would be saved:' . PHP_EOL;
foreach ($permissionIds as $id) {
    $permission = \App\Models\Permission::find($id);
    if ($permission) {
        echo '- ' . $permission->name . PHP_EOL;
    }
}

echo PHP_EOL;
echo '=== CHECKING IF PERMOHONAN PERMISSION IS INCLUDED ===' . PHP_EOL;
$permohonanPermission = \App\Models\Permission::where('name', 'permohonan')->first();
if ($permohonanPermission && in_array($permohonanPermission->id, $permissionIds)) {
    echo '✅ SUCCESS: permohonan permission will be saved' . PHP_EOL;
    echo '✅ Sidebar should work correctly for new users' . PHP_EOL;
} else {
    echo '❌ FAILED: permohonan permission will NOT be saved' . PHP_EOL;
}
