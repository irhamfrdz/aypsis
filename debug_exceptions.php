<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== TESTING WITH EXCEPTION HANDLING ===\n\n";

// Create UserController instance
$controller = new UserController();

// Test data
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

try {
    // Use the public test method with exception handling
    $permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrixData);

    echo "Result from public method:\n";
    print_r($permissionIds);
    echo "\n";

    if (!empty($permissionIds)) {
        echo "SUCCESS! Found permissions:\n";
        foreach ($permissionIds as $id) {
            $permission = Permission::find($id);
            if ($permission) {
                echo "- {$permission->name} (ID: {$id})\n";
            }
        }
    } else {
        echo "❌ FAILED: No permissions found (but no exception thrown)\n";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Throwable $t) {
    echo "❌ THROWABLE CAUGHT: " . $t->getMessage() . "\n";
    echo "Stack trace:\n" . $t->getTraceAsString() . "\n";
}

// Also test direct method call with reflection
echo "\n=== TESTING WITH REFLECTION ===\n";
try {
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertMatrixPermissionsToIds');
    $method->setAccessible(true);

    $permissionIds2 = $method->invoke($controller, $testMatrixData);

    echo "Result from reflection:\n";
    print_r($permissionIds2);
    echo "\n";

} catch (Exception $e) {
    echo "❌ REFLECTION EXCEPTION: " . $e->getMessage() . "\n";
} catch (Throwable $t) {
    echo "❌ REFLECTION THROWABLE: " . $t->getMessage() . "\n";
}

?>
