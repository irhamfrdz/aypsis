<?php
require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing permission conversion for user test4\n";
echo "===============================================\n\n";

// Find user test4
$user = \App\Models\User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Get current permissions
$permissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions:\n";
foreach ($permissions as $perm) {
    echo "  - {$perm}\n";
}
echo "\n";

// Test convertMatrixPermissionsToIds method
$controller = new \App\Http\Controllers\UserController();
$matrixPermissions = $controller->testConvertMatrixPermissionsToIds(['master-karyawan' => ['view' => '1']]);

    echo "Permission IDs for master-karyawan view:\n";
    foreach ($matrixPermissions as $id) {
        $perm = Permission::find($id);
        echo "  - {$id}: {$perm->name}\n";
    }
    echo "Total permissions found: " . count($matrixPermissions) . "\n";
echo "\n";

// Check what permissions user currently has
echo "User has permission for master-karyawan-view: " . ($user->can('master-karyawan-view') ? 'YES' : 'NO') . "\n";
echo "User has permission for master-karyawan-show: " . ($user->can('master-karyawan-show') ? 'YES' : 'NO') . "\n";

echo "\n🔍 Test completed!\n";

echo "Testing convertPermissionsToMatrix for tagihan-kontainer permissions:\n";
echo "=================================================================\n";

// Test permission names that would come from a user with tagihan-kontainer permissions
$permissionNames = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'tagihan-kontainer-update',
    'tagihan-kontainer-delete',
    'tagihan-kontainer-approve',
    'tagihan-kontainer-print',
    'tagihan-kontainer-export'
];

echo "Input permission names:\n";
foreach ($permissionNames as $name) {
    echo "- {$name}\n";
}
echo "\n";

// Use reflection to access the private method
$controller = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

// Call the method
try {
    $result = $method->invoke($controller, $permissionNames);
    echo "Method executed successfully!\n";
    echo "Result matrix:\n";

    foreach ($result as $module => $actions) {
        echo "Module: {$module}\n";
        foreach ($actions as $action => $value) {
            echo "  - {$action}: " . ($value ? 'true' : 'false') . "\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error calling method: " . $e->getMessage() . "\n";
}
?>
