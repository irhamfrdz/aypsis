<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== STEP-BY-STEP DEBUG: convertMatrixPermissionsToIds ===\n\n";

// Test data that simulates form submission
$testMatrixData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1'
        ]
    ]
];

echo "Input data:\n";
print_r($testMatrixData);
echo "\n";

// Extract the permissions array (this is what gets passed to convertMatrixPermissionsToIds)
$matrixPermissions = $testMatrixData['permissions'];

echo "=== STARTING CONVERSION ===\n";

foreach ($matrixPermissions as $module => $actions) {
    echo "\n1. Processing module: '{$module}'\n";

    // Skip if no actions are selected for this module
    if (!is_array($actions)) {
        echo "   ❌ Skipping: actions is not an array\n";
        continue;
    }
    echo "   ✅ Actions is array, proceeding...\n";

    foreach ($actions as $action => $value) {
        echo "\n2. Processing action: '{$action}' with value: '{$value}'\n";

        // Only process checked permissions (value = true or 1)
        if ($value == '1' || $value === true) {
            echo "   ✅ Value is '1' or true, proceeding...\n";

            // If the matrix action is 'access' or 'main', prefer a single module-level permission
            if ($action === 'access' || $action === 'main') {
                echo "   🔍 Action is 'access' or 'main', looking for module-level permission...\n";
                $modulePerm = Permission::where('name', $module)->first();
                if ($modulePerm) {
                    echo "   ✅ Found module permission: {$modulePerm->name} (ID: {$modulePerm->id})\n";
                    $permissionIds[] = $modulePerm->id;
                    continue; // done with this action
                }

                $dotModule = str_replace('-', '.', $module);
                $modulePermDot = Permission::where('name', $dotModule)->first();
                if ($modulePermDot) {
                    echo "   ✅ Found dot module permission: {$modulePermDot->name} (ID: {$modulePermDot->id})\n";
                    $permissionIds[] = $modulePermDot->id;
                    continue; // done with this action
                }
                echo "   ❌ No module-level permission found\n";
            }

            // Map matrix actions to database permission actions
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

            echo "   📋 Action map defined\n";

            $possibleActions = isset($actionMap[$action]) ? $actionMap[$action] : [$action];
            echo "   🔄 Possible actions for '{$action}': " . implode(', ', $possibleActions) . "\n";

            $found = false;

            // Check for master module pattern
            if (strpos($module, 'master-') === 0) {
                echo "   🏢 Module is master module, processing master logic...\n";
                // ... master module logic would go here
                echo "   (Skipping master module logic for this test)\n";
            }

            // Check for admin module
            if ($module === 'admin') {
                echo "   👑 Module is admin, processing admin logic...\n";
                // ... admin logic would go here
                echo "   (Skipping admin logic for this test)\n";
            }

            // Check for user-approval
            if ($module === 'user-approval') {
                echo "   👤 Module is user-approval, processing user-approval logic...\n";
                // ... user-approval logic would go here
                echo "   (Skipping user-approval logic for this test)\n";
            }

            // Special handling for pranota-supir and pembayaran-pranota-supir
            if (in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
                echo "   🎯 FOUND: pranota-supir special handling!\n";

                foreach ($possibleActions as $dbAction) {
                    echo "     🔍 Checking dbAction: '{$dbAction}'\n";

                    // Only process if the action from form matches the current dbAction
                    if ($action === $dbAction) {
                        echo "     ✅ Action '{$action}' matches dbAction '{$dbAction}'\n";

                        // Try dash notation first (pranota-supir-view)
                        $permissionName1 = $module . '-' . $dbAction;
                        echo "     🔍 Looking for dash notation: '{$permissionName1}'\n";
                        $permission1 = Permission::where('name', $permissionName1)->first();

                        if ($permission1) {
                            echo "     ✅ FOUND with dash notation: {$permission1->name} (ID: {$permission1->id})\n";
                            $permissionIds[] = $permission1->id;
                            $found = true;
                            break;
                        } else {
                            echo "     ❌ NOT found with dash notation\n";

                            // Try dot notation (pranota-supir.view)
                            $permissionName2 = $module . '.' . $dbAction;
                            echo "     🔍 Looking for dot notation: '{$permissionName2}'\n";
                            $permission2 = Permission::where('name', $permissionName2)->first();

                            if ($permission2) {
                                echo "     ✅ FOUND with dot notation: {$permission2->name} (ID: {$permission2->id})\n";
                                $permissionIds[] = $permission2->id;
                                $found = true;
                                break;
                            } else {
                                echo "     ❌ NOT found with dot notation either\n";
                            }
                        }
                    } else {
                        echo "     ❌ Action '{$action}' does not match dbAction '{$dbAction}'\n";
                    }
                }
            } else {
                echo "   ❌ Module '{$module}' is NOT in pranota-supir special handling\n";
            }

            echo "   📊 Found flag after special handling: " . ($found ? 'true' : 'false') . "\n";

            // If not found, try general patterns
            if (!$found) {
                echo "   🔄 Not found in special handling, trying general patterns...\n";
                // ... general pattern logic would go here
                echo "   (Skipping general patterns for this test)\n";
            }

        } else {
            echo "   ❌ Value is not '1' or true, skipping...\n";
        }
    }
}

echo "\n=== FINAL RESULT ===\n";
echo "Permission IDs found: " . print_r($permissionIds, true);

if (!empty($permissionIds)) {
    echo "\nPermission Details:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- ID {$id}: {$permission->name}\n";
        }
    }
} else {
    echo "\n❌ NO PERMISSIONS FOUND\n";
}

?>
