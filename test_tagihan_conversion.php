<?php
require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate the matrix permissions data that would come from the form
$matrixPermissions = [
    'tagihan-kontainer' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'approve' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

echo "Testing convertMatrixPermissionsToIds for tagihan-kontainer:\n";
echo "=======================================================\n";

// Use reflection to access the private method
$controller = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Call the method
try {
    $result = $method->invoke($controller, $matrixPermissions);
    echo "Method executed successfully!\n";
    echo "Returned permission IDs: " . implode(', ', $result) . "\n";

    // Check what permissions these IDs correspond to
    echo "\nCorresponding permissions:\n";
    foreach ($result as $id) {
        $perm = Permission::find($id);
        if ($perm) {
            echo "- ID {$id}: {$perm->name}\n";
        } else {
            echo "- ID {$id}: NOT FOUND\n";
        }
    }
} catch (Exception $e) {
    echo "Error calling method: " . $e->getMessage() . "\n";
}
?>
