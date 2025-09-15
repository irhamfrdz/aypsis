<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== COMPARING ORIGINAL METHOD VS DEBUG VERSION ===\n\n";

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

// Extract permissions array (this is what gets passed to convertMatrixPermissionsToIds)
$matrixPermissions = $testMatrixData['permissions'];

echo "Matrix Permissions (passed to method):\n";
print_r($matrixPermissions);
echo "\n";

// Test 1: Original method via reflection
echo "=== TEST 1: ORIGINAL METHOD VIA REFLECTION ===\n";
$controller = new UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

try {
    $result1 = $method->invoke($controller, $matrixPermissions);
    echo "Original method result: " . print_r($result1, true);
} catch (Exception $e) {
    echo "❌ ORIGINAL METHOD EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Test 2: Public test method
echo "\n=== TEST 2: PUBLIC TEST METHOD ===\n";
try {
    $result2 = $controller->testConvertMatrixPermissionsToIds($matrixPermissions);
    echo "Public test method result: " . print_r($result2, true);
} catch (Exception $e) {
    echo "❌ PUBLIC METHOD EXCEPTION: " . $e->getMessage() . "\n";
}

// Test 3: Manual debug version
echo "\n=== TEST 3: MANUAL DEBUG VERSION ===\n";
$permissionIds = [];
foreach ($matrixPermissions as $module => $actions) {
    echo "Processing module: '{$module}'\n";

    if (!is_array($actions)) continue;

    foreach ($actions as $action => $value) {
        echo "Processing action: '{$action}' with value: '{$value}'\n";

        if ($value == '1' || $value === true) {
            $actionMap = [
                'view' => ['index', 'show', 'view'],
                'create' => ['create', 'store'],
                'update' => ['edit', 'update'],
                'delete' => ['destroy', 'delete'],
                'print' => ['print'],
                'export' => ['export'],
                'import' => ['import'],
                'approve' => ['approve'],
                'access' => ['access'],
                'main' => ['main']
            ];

            $possibleActions = isset($actionMap[$action]) ? $actionMap[$action] : [$action];
            echo "Possible actions: " . implode(', ', $possibleActions) . "\n";

            $found = false;

            // Special handling for pranota-supir
            if (in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
                echo "Found pranota-supir special handling!\n";

                foreach ($possibleActions as $dbAction) {
                    if ($action === $dbAction) {
                        echo "Action '{$action}' matches dbAction '{$dbAction}'\n";

                        $permissionName1 = $module . '-' . $dbAction;
                        echo "Looking for: '{$permissionName1}'\n";
                        $permission1 = Permission::where('name', $permissionName1)->first();

                        if ($permission1) {
                            echo "✅ FOUND: {$permission1->name} (ID: {$permission1->id})\n";
                            $permissionIds[] = $permission1->id;
                            $found = true;
                            break;
                        } else {
                            echo "❌ NOT found: '{$permissionName1}'\n";

                            $permissionName2 = $module . '.' . $dbAction;
                            echo "Looking for: '{$permissionName2}'\n";
                            $permission2 = Permission::where('name', $permissionName2)->first();

                            if ($permission2) {
                                echo "✅ FOUND: {$permission2->name} (ID: {$permission2->id})\n";
                                $permissionIds[] = $permission2->id;
                                $found = true;
                                break;
                            } else {
                                echo "❌ NOT found: '{$permissionName2}'\n";
                            }
                        }
                    }
                }
            }

            echo "Found flag: " . ($found ? 'true' : 'false') . "\n";
        }
    }
}

echo "Manual debug result: " . print_r($permissionIds, true);

// Compare results
echo "\n=== COMPARISON ===\n";
echo "Original method: " . (isset($result1) ? count($result1) : 'ERROR') . " permissions\n";
echo "Public method: " . (isset($result2) ? count($result2) : 'ERROR') . " permissions\n";
echo "Manual debug: " . count($permissionIds) . " permissions\n";

if (isset($result1) && empty($result1) && !empty($permissionIds)) {
    echo "\n🔍 CONCLUSION: Original method has a bug - it returns empty array but manual logic works!\n";
}

?>
