<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING MASTER KODE NOMOR PERMISSIONS ===\n\n";

// Check permissions in database directly
$permissions = [
    'master-kode-nomor-view',
    'master-kode-nomor-create',
    'master-kode-nomor-update',
    'master-kode-nomor-delete'
];

foreach ($permissions as $perm) {
    $permission = DB::table('permissions')->where('name', $perm)->first();
    if ($permission) {
        echo "✓ Permission '$perm' exists (ID: {$permission->id})\n";
    } else {
        echo "✗ Permission '$perm' NOT FOUND\n";
    }
}

echo "\n=== CHECKING ADMIN USER PERMISSIONS ===\n";

// Check admin user permissions
$adminUser = DB::table('users')->where('username', 'admin')->first();
if ($adminUser) {
    echo "Admin user found: {$adminUser->username} (ID: {$adminUser->id})\n";

    // Check user permissions through user_permissions table
    $userPermissions = DB::table('permissions')
        ->join('user_permissions', 'permissions.id', '=', 'user_permissions.permission_id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->whereIn('permissions.name', $permissions)
        ->pluck('permissions.name')
        ->toArray();

    $allUserPermissions = $userPermissions;

    echo "Admin has permissions:\n";
    foreach ($permissions as $perm) {
        if (in_array($perm, $allUserPermissions)) {
            echo "✓ $perm\n";
        } else {
            echo "✗ $perm\n";
        }
    }
} else {
    echo "Admin user not found!\n";
}

echo "\n=== CHECKING ROUTES ===\n";

// Check if routes are registered
try {
    $routes = app('router')->getRoutes();
    $kodeNomorRoutes = [];

    foreach ($routes as $route) {
        if (strpos($route->getName(), 'master.kode-nomor') !== false) {
            $kodeNomorRoutes[] = $route->getName();
        }
    }

    if (count($kodeNomorRoutes) > 0) {
        echo "Found kode nomor routes:\n";
        foreach ($kodeNomorRoutes as $route) {
            echo "✓ $route\n";
        }
    } else {
        echo "✗ No kode nomor routes found!\n";
    }
} catch (Exception $e) {
    echo "Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n=== CHECKING CONTROLLER ===\n";

// Check if controller exists
$controllerPath = app_path('Http/Controllers/KodeNomorController.php');
if (file_exists($controllerPath)) {
    echo "✓ KodeNomorController exists at: $controllerPath\n";
} else {
    echo "✗ KodeNomorController NOT FOUND at: $controllerPath\n";
}

echo "\n=== CHECKING VIEWS ===\n";

// Check if views exist
$viewPaths = [
    resource_path('views/master/kode-nomor/index.blade.php'),
    resource_path('views/master/kode-nomor/create.blade.php'),
    resource_path('views/master/kode-nomor/edit.blade.php'),
    resource_path('views/master/kode-nomor/show.blade.php')
];

foreach ($viewPaths as $viewPath) {
    if (file_exists($viewPath)) {
        echo "✓ View exists: " . basename($viewPath) . "\n";
    } else {
        echo "✗ View NOT FOUND: " . basename($viewPath) . "\n";
    }
}
