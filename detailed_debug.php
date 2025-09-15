<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== DETAILED DEBUGGING convertPermissionsToMatrix ===\n\n";

// Create UserController instance
$userController = new App\Http\Controllers\UserController();

// Use reflection to access private methods
$reflection = new ReflectionClass($userController);
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);

// Test with a single permission
$testPermission = ['master.karyawan.index'];

echo "Testing with: " . json_encode($testPermission) . "\n\n";

try {
    $result = $convertToMatrixMethod->invoke($userController, [$testPermission]);
    echo "Final result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Let's also test the method directly by copying the code
echo "\n=== TESTING COPIED METHOD CODE ===\n";

function testConvertPermissionsToMatrix(array $permissionNames): array
{
    $matrixPermissions = [];

    foreach ($permissionNames as $permissionName) {
        echo "Processing: '$permissionName'\n";

        // Skip if not a string
        if (!is_string($permissionName)) {
            echo "  - Skipped: not a string\n";
            continue;
        }

        echo "  - Is string, continuing...\n";

        // Priority order: dot notation first, then dash notation, then simple

        // Pattern 1: module.submodule.action (e.g., master.karyawan.index) - HIGHEST PRIORITY
        if (strpos($permissionName, '.') !== false) {
            echo "  - Found dot, processing dot notation\n";
            $parts = explode('.', $permissionName);
            echo "  - Parts: " . json_encode($parts) . "\n";

            if (count($parts) >= 3 && $parts[0] === 'master') {
                echo "  - Master pattern matched\n";
                // For master.karyawan.index format
                $module = $parts[0] . '-' . $parts[1]; // master-karyawan
                $action = $parts[2]; // index
                echo "  - Module: $module, Action: $action\n";

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Map database actions to matrix actions
                $actionMap = [
                    'index' => 'view',
                    'create' => 'create',
                    'store' => 'create',
                    'show' => 'view',
                    'edit' => 'update', // edit in DB maps to update in matrix
                    'update' => 'update',
                    'destroy' => 'delete',
                    'print' => 'print',
                    'export' => 'export',
                    'import' => 'import',
                    'approve' => 'approve'
                ];

                $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                $matrixPermissions[$module][$mappedAction] = true;
                echo "  - Set matrixPermissions[$module][$mappedAction] = true\n";
                continue; // Skip other patterns
            } else {
                echo "  - Master pattern not matched\n";
            }
        } else {
            echo "  - No dot found\n";
        }

        echo "  - Continuing to next permission\n\n";
    }

    echo "Final matrix: " . json_encode($matrixPermissions, JSON_PRETTY_PRINT) . "\n";
    return $matrixPermissions;
}

$result2 = testConvertPermissionsToMatrix($testPermission);

echo "\n=== DEBUG COMPLETE ===\n";
