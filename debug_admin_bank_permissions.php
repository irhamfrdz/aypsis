<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== ADMIN USER BANK PERMISSIONS CHECK ===\n";

$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
    exit(1);
}

echo "✅ Admin user found: " . $admin->username . "\n";

// Check bank-related permissions in database
echo "\n--- Bank Permissions in Database ---\n";
$bankPermissions = Permission::where('name', 'like', '%bank%')->get();
foreach ($bankPermissions as $perm) {
    $hasPermission = $admin->permissions->contains('id', $perm->id);
    echo ($hasPermission ? '✅' : '❌') . " " . $perm->name . " (ID: " . $perm->id . ")\n";
}

// Check specific master-bank-view permission
echo "\n--- Specific master-bank-view Permission ---\n";
$viewBankPerm = Permission::where('name', 'master-bank-view')->first();
if ($viewBankPerm) {
    $hasViewPerm = $admin->permissions->contains('id', $viewBankPerm->id);
    echo ($hasViewPerm ? '✅' : '❌') . " Admin has master-bank-view permission\n";

    if ($hasViewPerm) {
        // Test Gate
        echo "\n--- Gate Testing ---\n";
        $gateDefined = Gate::has('master-bank-view');
        echo "Gate defined: " . ($gateDefined ? 'YES' : 'NO') . "\n";

        if ($gateDefined) {
            $gateAllows = Gate::allows('master-bank-view', $admin);
            echo "Gate allows: " . ($gateAllows ? 'YES' : 'NO') . "\n";

            $userCan = $admin->can('master-bank-view');
            echo "User->can(): " . ($userCan ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "\n--- Debug: Why admin doesn't have permission? ---\n";
        echo "Permission ID: " . $viewBankPerm->id . "\n";
        echo "Permission name: " . $viewBankPerm->name . "\n";
        echo "Admin permissions count: " . $admin->permissions->count() . "\n";

        // Check if there's a pivot table issue
        $pivotCheck = $admin->permissions()->where('permission_id', $viewBankPerm->id)->exists();
        echo "Pivot table check: " . ($pivotCheck ? 'EXISTS' : 'NOT EXISTS') . "\n";
    }
} else {
    echo "❌ master-bank-view permission not found in database\n";
}

// Check route access simulation
echo "\n--- Route Access Simulation ---\n";
$route = app('router')->getRoutes()->getByName('master-bank-index');
if ($route) {
    echo "✅ Route 'master-bank-index' exists\n";
    echo "Route URI: " . $route->uri() . "\n";

    $middleware = $route->middleware();
    echo "Middleware: " . implode(', ', $middleware) . "\n";

    // Check if middleware includes permission check
    $hasPermissionMiddleware = false;
    foreach ($middleware as $mw) {
        if (strpos($mw, 'can:master-bank-view') !== false) {
            $hasPermissionMiddleware = true;
            break;
        }
    }
    echo "Has permission middleware: " . ($hasPermissionMiddleware ? 'YES' : 'NO') . "\n";
} else {
    echo "❌ Route 'master-bank-index' not found\n";
}
