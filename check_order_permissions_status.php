<?php
// Include composer autoload and bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\User;

echo "=== CHECKING ORDER PERMISSIONS STATUS ===\n\n";

// 1. Check if order permissions exist in database
echo "1. Checking order permissions in database:\n";
$orderPermissions = Permission::where('name', 'LIKE', 'order-%')->get();

if ($orderPermissions->count() > 0) {
    foreach ($orderPermissions as $perm) {
        echo "   ✓ {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "   ❌ NO order-* permissions found!\n";
}

echo "\n";

// 2. Check Marlina's current permissions
echo "2. Checking Marlina's current permissions:\n";
$marlina = User::where('username', 'marlina')->first();

if ($marlina) {
    echo "   User: {$marlina->username} (ID: {$marlina->id})\n";
    
    $userPerms = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $marlina->id)
        ->where('permissions.name', 'LIKE', 'order-%')
        ->select('permissions.name', 'permissions.id')
        ->get();
    
    echo "   Order permissions:\n";
    if ($userPerms->count() > 0) {
        foreach ($userPerms as $perm) {
            echo "     ✓ {$perm->name}\n";
        }
    } else {
        echo "     ❌ NO order-* permissions found for Marlina!\n";
    }
} else {
    echo "   ❌ Marlina user not found!\n";
}

echo "\n";

// 3. Test the permission conversion logic
echo "3. Testing permission conversion logic:\n";
$testMatrixPermissions = [
    'order-management' => [
        'view' => '1',
        'create' => '1', 
        'update' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

echo "   Input matrix permissions:\n";
foreach ($testMatrixPermissions as $module => $actions) {
    foreach ($actions as $action => $value) {
        if ($value == '1') {
            echo "     - {$module}[{$action}]\n";
        }
    }
}

// Simulate the conversion logic from UserController
echo "\n   Expected database permission names:\n";
$actionMap = [
    'view' => 'order-view',
    'create' => 'order-create',
    'update' => 'order-update',
    'delete' => 'order-delete',
    'print' => 'order-print',
    'export' => 'order-export'
];

$expectedPermissions = [];
foreach ($testMatrixPermissions['order-management'] as $action => $value) {
    if ($value == '1' && isset($actionMap[$action])) {
        $permissionName = $actionMap[$action];
        echo "     - {$permissionName}\n";
        $expectedPermissions[] = $permissionName;
    }
}

echo "\n";

// 4. Check if expected permissions exist
echo "4. Checking if expected permissions exist in database:\n";
foreach ($expectedPermissions as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if ($perm) {
        echo "   ✓ {$permName} exists (ID: {$perm->id})\n";
    } else {
        echo "   ❌ {$permName} NOT FOUND in database!\n";
    }
}

echo "\n=== END ANALYSIS ===\n";