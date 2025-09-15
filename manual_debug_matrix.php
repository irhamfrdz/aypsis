<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== MANUAL DEBUGGING convertPermissionsToMatrix ===\n\n";

// Simulate the convertPermissionsToMatrix logic manually
function debugConvertPermissionsToMatrix(array $permissionNames): array
{
    $matrixPermissions = [];

    echo "Input permissions: " . json_encode($permissionNames) . "\n\n";

    foreach ($permissionNames as $permissionName) {
        echo "Processing: '$permissionName'\n";

        // Skip if not a string
        if (!is_string($permissionName)) {
            echo "  - Skipped: not a string\n";
            continue;
        }

        // Check for dot notation
        if (strpos($permissionName, '.') !== false) {
            echo "  - Contains dot, processing as dot notation\n";
            $parts = explode('.', $permissionName);
            echo "  - Parts: " . json_encode($parts) . "\n";

            if (count($parts) >= 3 && $parts[0] === 'master') {
                echo "  - Master module pattern detected\n";
                $module = $parts[0] . '-' . $parts[1];
                $action = $parts[2];
                echo "  - Module: $module, Action: $action\n";

                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $actionMap = [
                    'index' => 'view',
                    'create' => 'create',
                    'store' => 'create',
                    'show' => 'view',
                    'edit' => 'update',
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
                continue;
            } elseif (count($parts) >= 2) {
                echo "  - Other dot notation pattern\n";
                // Handle other patterns...
                echo "  - Would handle other patterns here\n";
            }
        }

        // Check for dash notation
        if (strpos($permissionName, '-') !== false) {
            echo "  - Contains dash, processing as dash notation\n";
            $parts = explode('-', $permissionName, 2);
            echo "  - Parts: " . json_encode($parts) . "\n";

            if (count($parts) == 2) {
                $module = $parts[0];
                $action = $parts[1];
                echo "  - Module: $module, Action: $action\n";

                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $actionMap = [
                    'view' => 'view',
                    'create' => 'create',
                    'update' => 'update',
                    'edit' => 'update',
                    'delete' => 'delete',
                    'destroy' => 'delete',
                    'print' => 'print',
                    'export' => 'export',
                    'import' => 'import',
                    'approve' => 'approve'
                ];

                $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                $matrixPermissions[$module][$mappedAction] = true;
                echo "  - Set matrixPermissions[$module][$mappedAction] = true\n";
                continue;
            }
        }

        // Simple module names
        if (strpos($permissionName, '-') === false && strpos($permissionName, '.') === false) {
            echo "  - Simple module name\n";
            $module = $permissionName;

            if (!isset($matrixPermissions[$module])) {
                $matrixPermissions[$module] = [];
            }

            $matrixPermissions[$module]['view'] = true;
            echo "  - Set matrixPermissions[$module][view] = true\n";
        }

        echo "\n";
    }

    echo "\nFinal result: " . json_encode($matrixPermissions, JSON_PRETTY_PRINT) . "\n";
    return $matrixPermissions;
}

// Test with specific permissions
$testPermissions = [
    'master.karyawan.index',
    'dashboard',
    'tagihan-kontainer-view'
];

$result = debugConvertPermissionsToMatrix($testPermissions);

echo "\n=== MANUAL DEBUG COMPLETE ===\n";
