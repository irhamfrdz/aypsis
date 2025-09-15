<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== DEBUGGING VARIABLE VALUES IN METHOD ===\n\n";

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Test data
$testMatrixData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1'
        ]
    ]
];

// Let's create a custom debug version that shows internal variables
echo "Creating debug version of the method...\n";

// We'll manually implement the logic with debug output to see variable values
$permissionIds = [];
$permissions = $testMatrixData['permissions'] ?? [];

echo "Input permissions: " . print_r($permissions, true);

foreach ($permissions as $module => $actions) {
    echo "\nProcessing module: '{$module}'\n";

    foreach ($actions as $action => $value) {
        echo "Processing action: '{$action}' with value: '{$value}'\n";

        if ($value != '1') {
            echo "Skipping because value != '1'\n";
            continue;
        }

        // Define possibleActions (this might be the issue)
        $possibleActions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
        echo "possibleActions defined as: " . print_r($possibleActions, true);

        $found = false;

        // Check if module is in special handling
        if (in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
            echo "✅ Module '{$module}' is in special handling\n";

            foreach ($possibleActions as $dbAction) {
                echo "  Checking dbAction: '{$dbAction}'\n";

                if ($action === $dbAction) {
                    echo "  ✅ Action '{$action}' matches dbAction '{$dbAction}'\n";

                    // Try dash notation
                    $permissionName1 = $module . '-' . $dbAction;
                    echo "  🔍 Looking for: '{$permissionName1}'\n";
                    $permission1 = Permission::where('name', $permissionName1)->first();

                    if ($permission1) {
                        echo "  ✅ FOUND: {$permission1->name} (ID: {$permission1->id})\n";
                        $permissionIds[] = $permission1->id;
                        $found = true;
                        break;
                    } else {
                        echo "  ❌ NOT found: '{$permissionName1}'\n";

                        // Try dot notation
                        $permissionName2 = $module . '.' . $dbAction;
                        echo "  🔍 Looking for: '{$permissionName2}'\n";
                        $permission2 = Permission::where('name', $permissionName2)->first();

                        if ($permission2) {
                            echo "  ✅ FOUND: {$permission2->name} (ID: {$permission2->id})\n";
                            $permissionIds[] = $permission2->id;
                            $found = true;
                            break;
                        } else {
                            echo "  ❌ NOT found: '{$permissionName2}'\n";
                        }
                    }
                } else {
                    echo "  ❌ Action '{$action}' does not match dbAction '{$dbAction}'\n";
                }
            }
        } else {
            echo "❌ Module '{$module}' is NOT in special handling\n";
        }

        echo "found flag after processing: " . ($found ? 'true' : 'false') . "\n";
    }
}

echo "\n=== FINAL RESULT ===\n";
echo "Permission IDs: " . print_r($permissionIds, true);

if (!empty($permissionIds)) {
    echo "Details:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- {$permission->name} (ID: {$id})\n";
        }
    }
}

?>
