<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== MANUAL DEBUG: Simulating convertMatrixPermissionsToIds with detailed logging ===\n\n";

// Test data with correct format
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

// Manually implement the convertMatrixPermissionsToIds logic with debug output
$permissionIds = [];
$permissions = $testMatrixData['permissions'] ?? [];

echo "Step 1: Processing permissions array\n";
echo "Permissions found: " . count($permissions) . " modules\n";

foreach ($permissions as $module => $actions) {
    echo "\nStep 2: Processing module '{$module}'\n";
    echo "Actions: " . print_r($actions, true);

    foreach ($actions as $action => $value) {
        echo "\nStep 3: Processing action '{$action}' with value '{$value}'\n";

        // Check value condition
        if ($value != '1') {
            echo "❌ Skipping: value is not '1' (value = '{$value}')\n";
            continue;
        }
        echo "✅ Value is '1', proceeding...\n";

        // Check for pranota-supir special handling
        if ($module === 'pranota' && strpos($action, 'supir-') === 0) {
            echo "✅ MATCH: Found pranota + supir- pattern\n";
            $action = str_replace('supir-', '', $action);
            $module = 'pranota-supir';
            echo "🔄 Transformed: module='{$module}', action='{$action}'\n";
        }

        // Check for pranota-supir special handling (the one that should work)
        if (in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
            echo "✅ MATCH: Found pranota-supir special handling\n";

            $possibleActions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];

            foreach ($possibleActions as $dbAction) {
                echo "  Trying dbAction: '{$dbAction}'\n";

                if ($action === $dbAction) {
                    echo "  ✅ Action matches dbAction: '{$action}'\n";

                    // Try dash notation first
                    $permissionName1 = $module . '-' . $dbAction;
                    echo "  🔍 Looking for dash notation: '{$permissionName1}'\n";
                    $permission1 = Permission::where('name', $permissionName1)->first();

                    if ($permission1) {
                        echo "  ✅ FOUND with dash notation: {$permission1->name} (ID: {$permission1->id})\n";
                        $permissionIds[] = $permission1->id;
                        break;
                    } else {
                        echo "  ❌ NOT found with dash notation\n";

                        // Try dot notation
                        $permissionName2 = $module . '.' . $dbAction;
                        echo "  🔍 Looking for dot notation: '{$permissionName2}'\n";
                        $permission2 = Permission::where('name', $permissionName2)->first();

                        if ($permission2) {
                            echo "  ✅ FOUND with dot notation: {$permission2->name} (ID: {$permission2->id})\n";
                            $permissionIds[] = $permission2->id;
                            break;
                        } else {
                            echo "  ❌ NOT found with dot notation either\n";
                        }
                    }
                } else {
                    echo "  ❌ Action '{$action}' does not match dbAction '{$dbAction}'\n";
                }
            }
        } else {
            echo "❌ Module '{$module}' is not in pranota-supir special handling\n";
        }

        // If not in special handling, try general patterns
        if (!in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
            echo "🔄 Not in special handling, trying general patterns...\n";

            // Try dash notation
            $permissionName = $module . '-' . $action;
            echo "🔍 Trying general dash notation: '{$permissionName}'\n";
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                echo "✅ FOUND with general pattern: {$permission->name} (ID: {$permission->id})\n";
                $permissionIds[] = $permission->id;
            } else {
                echo "❌ NOT found with general pattern\n";
            }
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
    echo "\n❌ NO PERMISSIONS FOUND - This confirms the bug!\n";
}

?>
