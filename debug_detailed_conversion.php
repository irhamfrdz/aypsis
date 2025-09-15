<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== DETAILED DEBUG PERMISSION CONVERSION ===\n\n";

// Simulasi data matrix
$matrixData = [
    'master-karyawan' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ]
];

echo "Input matrix data:\n";
print_r($matrixData);
echo "\n";

// Manual simulation of convertMatrixPermissionsToIds logic
$permissionIds = [];
$actionMap = [
    'view' => ['index', 'show', 'view'],
    'create' => ['create', 'store'],
    'update' => ['edit', 'update'], // edit comes first for database lookup
    'delete' => ['destroy', 'delete'],
    'print' => ['print'],
    'export' => ['export'],
    'import' => ['import'],
    'approve' => ['approve'],
    'access' => ['access']
];

foreach ($matrixData as $module => $actions) {
    echo "Processing module: $module\n";

    foreach ($actions as $action => $value) {
        if ($value == '1') {
            echo "  Processing action: $action\n";

            $possibleActions = isset($actionMap[$action]) ? $actionMap[$action] : [$action];
            echo "    Possible DB actions: " . implode(', ', $possibleActions) . "\n";

            $found = false;

            // Special handling for master-* modules
            if (strpos($module, 'master-') === 0) {
                echo "    Detected master module, converting format...\n";

                $moduleParts = explode('-', $module);
                if (count($moduleParts) >= 2) {
                    $baseModule = $moduleParts[0]; // master
                    $subModule = $moduleParts[1]; // karyawan
                    echo "    Base module: $baseModule, Sub module: $subModule\n";

                    // Try master.submodule.action pattern - PRIORITY for master modules
                    foreach ($possibleActions as $dbAction) {
                        $permissionName = $baseModule . '.' . $subModule . '.' . $dbAction;
                        echo "      Trying: $permissionName\n";

                        $permission = Permission::where('name', $permissionName)->first();

                        if ($permission) {
                            echo "        ✅ FOUND: $permissionName (ID: {$permission->id})\n";
                            $permissionIds[] = $permission->id;
                            $found = true;
                            break 2; // Break out of both loops
                        } else {
                            echo "        ❌ NOT FOUND: $permissionName\n";
                        }
                    }
                }
            }

            if (!$found) {
                echo "    No permission found for $module.$action\n";
            }
        }
    }
}

echo "\nFinal permission IDs: " . implode(', ', $permissionIds) . "\n";

echo "\n=== PERMISSION DETAILS ===\n";
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "✓ {$perm->name} (ID: {$perm->id})\n";
    }
}

echo "\n=== DONE ===\n";
