<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Testing Pergerakan Kontainer Permissions ===\n\n";

// 1. Check if permissions exist in database
echo "1. Checking permissions in database:\n";
echo str_repeat("-", 60) . "\n";

$permissions = DB::table('permissions')
    ->where('name', 'like', 'pergerakan-kontainer%')
    ->orderBy('id')
    ->get(['id', 'name']);

if ($permissions->isEmpty()) {
    echo "❌ NO PERMISSIONS FOUND!\n\n";
} else {
    foreach ($permissions as $perm) {
        echo "  ✓ ID: {$perm->id} - {$perm->name}\n";
    }
    echo "\nTotal: {$permissions->count()} permissions\n\n";
}

// 2. Test the conversion logic
echo "2. Testing conversion logic:\n";
echo str_repeat("-", 60) . "\n";

$testMatrix = [
    'pergerakan-kontainer' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'approve' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

echo "Testing matrix:\n";
print_r($testMatrix);
echo "\n";

// Simulate the controller logic
$permissionIds = [];
$module = 'pergerakan-kontainer';
$actions = $testMatrix[$module];

foreach ($actions as $action => $value) {
    if ($value == '1' || $value === true) {
        $actionMap = [
            'view' => 'pergerakan-kontainer-view',
            'create' => 'pergerakan-kontainer-create',
            'update' => 'pergerakan-kontainer-update',
            'delete' => 'pergerakan-kontainer-delete',
            'approve' => 'pergerakan-kontainer-approve',
            'print' => 'pergerakan-kontainer-print',
            'export' => 'pergerakan-kontainer-export'
        ];

        if (isset($actionMap[$action])) {
            $permissionName = $actionMap[$action];
            $directPermission = DB::table('permissions')->where('name', $permissionName)->first();
            
            if ($directPermission) {
                $permissionIds[] = $directPermission->id;
                echo "  ✓ Found: {$action} -> {$permissionName} (ID: {$directPermission->id})\n";
            } else {
                echo "  ❌ NOT FOUND: {$action} -> {$permissionName}\n";
            }
        }
    }
}

echo "\nTotal permission IDs found: " . count($permissionIds) . "\n";
echo "Permission IDs: " . implode(', ', $permissionIds) . "\n\n";

// 3. Check if any user has these permissions
echo "3. Checking user assignments:\n";
echo str_repeat("-", 60) . "\n";

$assignments = DB::table('user_permissions')
    ->whereIn('permission_id', $permissions->pluck('id'))
    ->join('users', 'user_permissions.user_id', '=', 'users.id')
    ->select('users.username', 'user_permissions.permission_id')
    ->get();

if ($assignments->isEmpty()) {
    echo "⚠ No users have these permissions assigned yet.\n\n";
} else {
    echo "Users with pergerakan-kontainer permissions:\n";
    $groupedByUser = $assignments->groupBy('username');
    foreach ($groupedByUser as $username => $perms) {
        echo "  - {$username}: {$perms->count()} permissions\n";
    }
    echo "\n";
}

// 4. Check database table structure
echo "4. Checking table structure:\n";
echo str_repeat("-", 60) . "\n";

$tables = DB::select("SHOW TABLES LIKE 'user_permissions'");
if (empty($tables)) {
    echo "❌ Table 'user_permissions' does NOT exist!\n\n";
} else {
    echo "✓ Table 'user_permissions' exists\n";
    $columns = DB::select("DESCRIBE user_permissions");
    echo "Columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    echo "\n";
}

echo "=== Test Complete ===\n";
