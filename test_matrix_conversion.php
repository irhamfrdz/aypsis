<?php
require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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
