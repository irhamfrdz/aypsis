<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== TESTING WITH SIMPLIFIED METHOD ===\n\n";

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

// Let's manually implement a simplified version of the method
function debugConvertMatrixPermissionsToIds($matrixPermissions) {
    $permissionIds = [];
    echo "Starting conversion...\n";

    foreach ($matrixPermissions as $module => $actions) {
        echo "Processing module: {$module}\n";

        if (!is_array($actions)) {
            echo "Skipping non-array actions\n";
            continue;
        }

        foreach ($actions as $action => $value) {
            echo "Processing action: {$action} = {$value}\n";

            if ($value != '1' && $value !== true) {
                echo "Skipping because value is not '1' or true\n";
                continue;
            }

            echo "Value is valid, looking for permissions...\n";

            // Special handling for pranota-supir
            if (in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
                echo "Found pranota-supir special handling\n";

                $possibleActions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];

                foreach ($possibleActions as $dbAction) {
                    echo "  Checking dbAction: {$dbAction}\n";

                    if ($action === $dbAction) {
                        echo "  ✅ Action matches! Looking for permission...\n";

                        // Try dash notation
                        $permissionName1 = $module . '-' . $dbAction;
                        echo "  🔍 Trying: {$permissionName1}\n";
                        $permission1 = Permission::where('name', $permissionName1)->first();

                        if ($permission1) {
                            echo "  ✅ FOUND: {$permission1->name} (ID: {$permission1->id})\n";
                            $permissionIds[] = $permission1->id;
                            break;
                        } else {
                            echo "  ❌ NOT found: {$permissionName1}\n";

                            // Try dot notation
                            $permissionName2 = $module . '.' . $dbAction;
                            echo "  🔍 Trying: {$permissionName2}\n";
                            $permission2 = Permission::where('name', $permissionName2)->first();

                            if ($permission2) {
                                echo "  ✅ FOUND: {$permission2->name} (ID: {$permission2->id})\n";
                                $permissionIds[] = $permission2->id;
                                break;
                            } else {
                                echo "  ❌ NOT found: {$permissionName2}\n";
                            }
                        }
                    } else {
                        echo "  ❌ Action '{$action}' does not match dbAction '{$dbAction}'\n";
                    }
                }
            } else {
                echo "Module '{$module}' is not in special handling\n";
            }
        }
    }

    echo "Conversion complete. Permission IDs: " . print_r($permissionIds, true);
    return $permissionIds;
}

// Test the simplified method
$result = debugConvertMatrixPermissionsToIds($testMatrixData['permissions']);

echo "\nFinal result: " . print_r($result, true);

if (!empty($result)) {
    echo "Details:\n";
    foreach ($result as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- {$permission->name} (ID: {$id})\n";
        }
    }
}

?>
