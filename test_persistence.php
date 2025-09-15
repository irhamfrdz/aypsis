<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing permission persistence after save and reload\n";
echo "===================================================\n\n";

// Test data - user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Check current permissions in database
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions in database:\n";
foreach ($currentPermissions as $perm) {
    echo "  - $perm\n";
}
echo "\n";

// Simulate what happens when user opens edit page
$controller = new UserController();
$reflection = new ReflectionClass($controller);

// Test convertPermissionsToMatrix method
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$matrixResult = $method->invoke($controller, $currentPermissions);

echo "Matrix result sent to view:\n";
foreach ($matrixResult as $module => $actions) {
    echo "Module: $module\n";
    if (is_array($actions)) {
        foreach ($actions as $action => $value) {
            echo "  - $action: " . ($value ? 'true' : 'false') . "\n";
        }
    } else {
        echo "  - Value: $actions\n";
    }
    echo "\n";
}

// Check what the view would actually see
echo "What the view checkboxes would show:\n";

$modulesToCheck = ['tagihan-kontainer', 'master-pranota-tagihan-kontainer'];
foreach ($modulesToCheck as $module) {
    echo "Module: $module\n";
    if (isset($matrixResult[$module])) {
        if ($module === 'tagihan-kontainer') {
            $actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
            foreach ($actions as $action) {
                $checked = isset($matrixResult[$module][$action]) && $matrixResult[$module][$action];
                echo "  - $action: " . ($checked ? 'CHECKED' : 'unchecked') . "\n";
            }
        } elseif ($module === 'master-pranota-tagihan-kontainer') {
            $checked = isset($matrixResult[$module]['access']) && $matrixResult[$module]['access'];
            echo "  - access: " . ($checked ? 'CHECKED' : 'unchecked') . "\n";
        }
    } else {
        echo "  - Module not found in matrix!\n";
    }
    echo "\n";
}

// Test if the permissions are actually saved correctly
echo "Verifying permission IDs in database:\n";
$permissionIds = $user->permissions->pluck('id')->toArray();
echo "Permission IDs: " . implode(', ', $permissionIds) . "\n";

$expectedIds = [265, 266, 267, 268, 269, 270, 271, 133]; // tagihan-kontainer-* + master-pranota-tagihan-kontainer
$missingIds = array_diff($expectedIds, $permissionIds);
$extraIds = array_diff($permissionIds, $expectedIds);

if (empty($missingIds) && empty($extraIds)) {
    echo "✅ All expected permissions are saved correctly\n";
} else {
    if (!empty($missingIds)) {
        echo "❌ Missing permission IDs: " . implode(', ', $missingIds) . "\n";
        foreach ($missingIds as $id) {
            $perm = Permission::find($id);
            if ($perm) {
                echo "  - ID $id: {$perm->name}\n";
            }
        }
    }
    if (!empty($extraIds)) {
        echo "❌ Extra permission IDs: " . implode(', ', $extraIds) . "\n";
        foreach ($extraIds as $id) {
            $perm = Permission::find($id);
            if ($perm) {
                echo "  - ID $id: {$perm->name}\n";
            }
        }
    }
}

echo "\nTest completed!\n";
