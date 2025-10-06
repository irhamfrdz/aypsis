<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simple Admin Permission Check ===\n\n";

// Check permissions table structure
echo "Permissions table columns:\n";
$columns = DB::select('DESCRIBE permissions');
foreach($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

// Get admin user
$adminUser = DB::table('users')->where('username', 'admin')->first();
echo "\nAdmin User ID: {$adminUser->id}\n";

// Check admin role permissions - simplified query
$adminPermissions = DB::select("
    SELECT p.name
    FROM permission_role pr
    JOIN permissions p ON pr.permission_id = p.id
    JOIN role_user ru ON pr.role_id = ru.role_id
    WHERE ru.user_id = ?
", [$adminUser->id]);

echo "\nAdmin permissions (" . count($adminPermissions) . " total):\n";
$pranotaPerms = [];
foreach($adminPermissions as $perm) {
    if (strpos(strtolower($perm->name), 'pranota') !== false ||
        strpos(strtolower($perm->name), 'import') !== false) {
        $pranotaPerms[] = $perm->name;
        echo "✓ {$perm->name}\n";
    }
}

if (empty($pranotaPerms)) {
    echo "❌ No pranota/import permissions found!\n";

    echo "\nSearching for permissions that might be needed:\n";
    $allPerms = DB::select("SELECT name FROM permissions WHERE name LIKE '%pranota%' OR name LIKE '%import%'");
    foreach($allPerms as $perm) {
        echo "- Available: {$perm->name}\n";
    }
}

// Check if there's a middleware issue by testing the route
echo "\n=== Route Access Test ===\n";

try {
    // Check web routes file for import route
    if (file_exists(base_path('routes/web.php'))) {
        $routeContent = file_get_contents(base_path('routes/web.php'));
        if (strpos($routeContent, 'import-csv') !== false) {
            echo "✓ Import CSV route found in web.php\n";
        } else {
            echo "❌ Import CSV route not found in web.php\n";
        }
    }

    // Test access to the route
    $routes = app('router')->getRoutes();
    foreach($routes as $route) {
        if (strpos($route->uri(), 'pranota') !== false && strpos($route->uri(), 'import') !== false) {
            echo "Found route: " . $route->uri() . "\n";
            $middleware = $route->middleware();
            echo "Middleware: " . implode(', ', $middleware) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
