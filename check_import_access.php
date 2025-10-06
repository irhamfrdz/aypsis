<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Check Import Pranota Access Issue ===\n\n";

// Check current user permissions for admin
$adminUser = DB::table('users')->where('username', 'admin')->orWhere('email', 'admin@example.com')->first();

if ($adminUser) {
    echo "Admin User Found:\n";
    echo "- ID: {$adminUser->id}\n";
    echo "- Username: {$adminUser->username}\n";
    echo "- Email: {$adminUser->email}\n\n";

    // Check roles
    $roles = DB::table('role_user')
        ->join('roles', 'role_user.role_id', '=', 'roles.id')
        ->where('role_user.user_id', $adminUser->id)
        ->select('roles.name')
        ->get();

    echo "Roles:\n";
    foreach($roles as $role) {
        echo "- {$role->name}\n";
    }

    // Check permissions related to pranota
    $permissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->where('permissions.name', 'like', '%pranota%')
        ->orWhere('permissions.name', 'like', '%import%')
        ->select('permissions.name', 'permissions.guard_name')
        ->get();

    echo "\nPranota/Import Related Permissions:\n";
    foreach($permissions as $perm) {
        echo "- {$perm->name} ({$perm->guard_name})\n";
    }

    // Check role permissions
    $rolePermissions = DB::table('permission_role')
        ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
        ->join('role_user', function($join) use ($adminUser) {
            $join->on('permission_role.role_id', '=', 'role_user.role_id')
                 ->where('role_user.user_id', '=', $adminUser->id);
        })
        ->where('permissions.name', 'like', '%pranota%')
        ->orWhere('permissions.name', 'like', '%import%')
        ->select('permissions.name', 'permissions.guard_name')
        ->distinct()
        ->get();

    echo "\nRole-based Pranota/Import Permissions:\n";
    foreach($rolePermissions as $perm) {
        echo "- {$perm->name} ({$perm->guard_name})\n";
    }
} else {
    echo "❌ Admin user not found!\n";
}

// Check available pranota permissions
echo "\n=== All Pranota/Import Permissions in System ===\n";
$allPranotaPermissions = DB::table('permissions')
    ->where('name', 'like', '%pranota%')
    ->orWhere('name', 'like', '%import%')
    ->get();

foreach($allPranotaPermissions as $perm) {
    echo "- {$perm->name} ({$perm->guard_name})\n";
}

// Check routes that might need permission
echo "\n=== Checking Route Permissions ===\n";

// List routes that start with pranota
$routeCollection = app('router')->getRoutes();
foreach ($routeCollection as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'pranota') !== false) {
        $methods = implode('|', $route->methods());
        $name = $route->getName() ?: 'unnamed';
        echo "- {$methods} {$uri} -> {$name}\n";
    }
}

?>
