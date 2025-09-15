<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== DETAILED DEBUG: Step-by-step pranota-supir conversion ===\n\n";

// Simulate the exact data structure from form submission
$testMatrixData = [
    'permissions' => [
        'pranota' => [
            'supir-view' => '1'
        ]
    ]
];

echo "Input Matrix Data:\n";
print_r($testMatrixData);
echo "\n";

// Manually simulate the convertMatrixPermissionsToIds logic
$permissionIds = [];
$permissions = $testMatrixData['permissions'] ?? [];

echo "Processing permissions array...\n";
foreach ($permissions as $module => $actions) {
    echo "Module: {$module}\n";
    echo "Actions: " . print_r($actions, true);

    foreach ($actions as $action => $value) {
        echo "  Processing action: {$action} = {$value}\n";

        if ($value != '1') {
            echo "  Skipping because value is not '1'\n";
            continue;
        }

        // Check if this matches pranota-supir pattern
        if ($module === 'pranota' && strpos($action, 'supir-') === 0) {
            echo "  ✓ MATCH: pranota + supir- pattern detected\n";
            $action = str_replace('supir-', '', $action); // Remove 'supir-' prefix
            $module = 'pranota-supir'; // Set module to pranota-supir
            echo "  After transformation: module='{$module}', action='{$action}'\n";
        }

        // Now try to find the permission
        echo "  Looking for permission with module='{$module}', action='{$action}'\n";

        // Try dash notation first (pranota-supir-view)
        $permissionName1 = $module . '-' . $action;
        echo "  Trying dash notation: '{$permissionName1}'\n";
        $permission1 = Permission::where('name', $permissionName1)->first();

        if ($permission1) {
            echo "  ✓ FOUND with dash notation: {$permission1->name} (ID: {$permission1->id})\n";
            $permissionIds[] = $permission1->id;
        } else {
            echo "  ✗ NOT found with dash notation\n";

            // Try dot notation (pranota-supir.view)
            $permissionName2 = $module . '.' . $action;
            echo "  Trying dot notation: '{$permissionName2}'\n";
            $permission2 = Permission::where('name', $permissionName2)->first();

            if ($permission2) {
                echo "  ✓ FOUND with dot notation: {$permission2->name} (ID: {$permission2->id})\n";
                $permissionIds[] = $permission2->id;
            } else {
                echo "  ✗ NOT found with dot notation either\n";
            }
        }
    }
}

echo "\nFinal Permission IDs: " . print_r($permissionIds, true);

if (!empty($permissionIds)) {
    echo "\nCorresponding Permission Names:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- ID {$id}: {$permission->name}\n";
        }
    }
} else {
    echo "\n❌ No permissions found - this is the problem!\n";
}

?>
