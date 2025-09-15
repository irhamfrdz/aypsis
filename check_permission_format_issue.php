<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo '=== CHECKING PERMISSION NAMING FORMAT ===' . PHP_EOL;

$permohonanPermissions = Permission::where('name', 'like', '%permohonan%')->get();

echo 'All permohonan-related permissions in database:' . PHP_EOL;
foreach ($permohonanPermissions as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL;
echo '=== CHECKING ROUTE MIDDLEWARE REQUIREMENTS ===' . PHP_EOL;

// Check what permission the route actually requires
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'permohonan/create')) {
        $middleware = $route->middleware();
        echo 'Route: ' . $route->uri() . PHP_EOL;
        echo 'Methods: ' . implode(', ', $route->methods()) . PHP_EOL;
        echo 'Middleware: ' . implode(', ', $middleware) . PHP_EOL;

        // Check if there's a 'can:' middleware
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'can:')) {
                $requiredPermission = str_replace('can:', '', $mw);
                echo 'Required permission: ' . $requiredPermission . PHP_EOL;
            }
        }
        echo PHP_EOL;
        break; // Only check the first matching route
    }
}

echo '=== ANALYSIS ===' . PHP_EOL;
$hasDashFormat = Permission::where('name', 'permohonan-create')->exists();
$hasDotFormat = Permission::where('name', 'permohonan.create')->exists();

echo 'Permission with dash format (permohonan-create): ' . ($hasDashFormat ? '✅ EXISTS' : '❌ NOT FOUND') . PHP_EOL;
echo 'Permission with dot format (permohonan.create): ' . ($hasDotFormat ? '✅ EXISTS' : '❌ NOT FOUND') . PHP_EOL;

if ($hasDashFormat && !$hasDotFormat) {
    echo PHP_EOL . '🔍 ISSUE: System uses dot format but user has dash format permission!' . PHP_EOL;
    echo 'Solution: Either:' . PHP_EOL;
    echo '1. Change user permission from permohonan-create to permohonan.create' . PHP_EOL;
    echo '2. Or update route middleware to use dash format' . PHP_EOL;
} elseif (!$hasDashFormat && $hasDotFormat) {
    echo PHP_EOL . '🔍 ISSUE: User has dash format but system expects dot format!' . PHP_EOL;
    echo 'Solution: Either:' . PHP_EOL;
    echo '1. Change user permission from permohonan-create to permohonan.create' . PHP_EOL;
    echo '2. Or update route middleware to use dash format' . PHP_EOL;
} else {
    echo PHP_EOL . '✅ Permission formats are consistent' . PHP_EOL;
}
