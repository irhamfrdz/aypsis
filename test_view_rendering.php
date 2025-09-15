<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing view rendering simulation\n";
echo "=================================\n\n";

// Test data - user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Get current permissions
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions:\n";
foreach ($currentPermissions as $perm) {
    echo "  - $perm\n";
}
echo "\n";

// Simulate what happens in the edit method
$controller = new UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$userMatrixPermissions = $method->invoke($controller, $currentPermissions);

echo "User Matrix Permissions (sent to view):\n";
print_r($userMatrixPermissions);
echo "\n";

// Simulate Blade template condition checking
echo "Simulating Blade template condition checking:\n\n";

$modules = ['tagihan-kontainer', 'master-pranota-tagihan-kontainer'];

foreach ($modules as $module) {
    echo "Module: $module\n";

    if ($module === 'tagihan-kontainer') {
        $actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];

        foreach ($actions as $action) {
            // Simulate: @if(isset($userMatrixPermissions['tagihan-kontainer']['view']) && $userMatrixPermissions['tagihan-kontainer']['view'])
            $condition1 = isset($userMatrixPermissions[$module][$action]);
            $condition2 = $condition1 && $userMatrixPermissions[$module][$action];

            echo "  - $action: isset()=" . ($condition1 ? 'true' : 'false') .
                 ", value=" . ($condition1 ? ($userMatrixPermissions[$module][$action] ? 'true' : 'false') : 'N/A') .
                 ", final=" . ($condition2 ? 'CHECKED' : 'unchecked') . "\n";
        }
    } elseif ($module === 'master-pranota-tagihan-kontainer') {
        // Simulate: @if(isset($userMatrixPermissions['master-pranota-tagihan-kontainer']['access']) && $userMatrixPermissions['master-pranota-tagihan-kontainer']['access'])
        $condition1 = isset($userMatrixPermissions[$module]['access']);
        $condition2 = $condition1 && $userMatrixPermissions[$module]['access'];

        echo "  - access: isset()=" . ($condition1 ? 'true' : 'false') .
             ", value=" . ($condition1 ? ($userMatrixPermissions[$module]['access'] ? 'true' : 'false') : 'N/A') .
             ", final=" . ($condition2 ? 'CHECKED' : 'unchecked') . "\n";
    }

    echo "\n";
}

// Test if there are any issues with the data types
echo "Data type verification:\n";
foreach ($userMatrixPermissions as $module => $actions) {
    echo "Module: $module (" . gettype($actions) . ")\n";
    if (is_array($actions)) {
        foreach ($actions as $action => $value) {
            echo "  - $action: " . gettype($value) . " = " . ($value ? 'true' : 'false') . "\n";
        }
    } else {
        echo "  - Value: " . gettype($actions) . " = $actions\n";
    }
    echo "\n";
}

echo "Test completed!\n";
