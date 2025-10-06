<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Check Import Pranota Access Issue ===\n\n";

// Check users table structure
echo "Users table columns:\n";
$columns = DB::select('DESCRIBE users');
foreach($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

// Check admin user with correct column
$adminUser = DB::table('users')->where('username', 'admin')->first();

if ($adminUser) {
    echo "\nAdmin User Found:\n";
    echo "- ID: {$adminUser->id}\n";
    echo "- Username: {$adminUser->username}\n\n";

    // Check roles
    $roles = DB::table('role_user')
        ->join('roles', 'role_user.role_id', '=', 'roles.id')
        ->where('role_user.user_id', $adminUser->id)
        ->select('roles.name')
        ->get();

    echo "Admin Roles:\n";
    foreach($roles as $role) {
        echo "- {$role->name}\n";
    }

    // Check direct permissions
    $directPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->select('permissions.name', 'permissions.guard_name')
        ->get();

    echo "\nDirect User Permissions: " . count($directPermissions) . " total\n";

    // Check role-based permissions
    $roleIds = $roles->pluck('name');
    if (!empty($roleIds)) {
        $rolePermissions = DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->join('roles', 'permission_role.role_id', '=', 'roles.id')
            ->whereIn('roles.name', $roleIds)
            ->select('permissions.name', 'permissions.guard_name', 'roles.name as role_name')
            ->get();

        echo "\nRole-based Permissions: " . count($rolePermissions) . " total\n";

        // Check for pranota/import related permissions
        $pranotaPerms = $rolePermissions->filter(function($perm) {
            return strpos(strtolower($perm->name), 'pranota') !== false ||
                   strpos(strtolower($perm->name), 'import') !== false;
        });

        echo "\nPranota/Import Permissions:\n";
        foreach($pranotaPerms as $perm) {
            echo "- {$perm->name} (via role: {$perm->role_name})\n";
        }
    }
} else {
    echo "❌ Admin user not found! Available users:\n";
    $users = DB::table('users')->select('id', 'username')->get();
    foreach($users as $user) {
        echo "- ID: {$user->id}, Username: {$user->username}\n";
    }
}

// Check if there are any middleware issues
echo "\n=== Available Pranota Routes ===\n";
try {
    $routes = app('router')->getRoutes();
    foreach($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'pranota') !== false && strpos($uri, 'import') !== false) {
            $methods = implode('|', $route->methods());
            $name = $route->getName() ?: 'unnamed';
            $action = $route->getAction();
            $middleware = $action['middleware'] ?? [];

            echo "- {$methods} {$uri}\n";
            echo "  Name: {$name}\n";
            echo "  Middleware: " . implode(', ', $middleware) . "\n\n";
        }
    }
} catch (Exception $e) {
    echo "Error getting routes: " . $e->getMessage() . "\n";
}

?>
