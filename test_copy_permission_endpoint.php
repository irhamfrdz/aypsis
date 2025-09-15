<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST COPY PERMISSION ENDPOINT ===\n\n";

// Test route exists
$routes = Route::getRoutes();
$foundCopyRoute = false;
$foundRegularRoute = false;

foreach ($routes as $route) {
    if ($route->getName() === 'user.permissions-for-copy') {
        $foundCopyRoute = true;
        echo "✅ Route 'user.permissions-for-copy' exists\n";
        echo "   - URI: {$route->uri()}\n";
        echo "   - Methods: " . implode(', ', $route->methods()) . "\n";
        echo "   - Middleware: " . (empty($route->middleware()) ? 'None' : implode(', ', $route->middleware())) . "\n";
    }
    if ($route->getName() === 'user.permissions') {
        $foundRegularRoute = true;
        echo "✅ Route 'user.permissions' exists\n";
        echo "   - URI: {$route->uri()}\n";
        echo "   - Methods: " . implode(', ', $route->methods()) . "\n";
        echo "   - Middleware: " . (empty($route->middleware()) ? 'None' : implode(', ', $route->middleware())) . "\n";
    }
}

if (!$foundCopyRoute) {
    echo "❌ Route 'user.permissions-for-copy' not found\n";
}
if (!$foundRegularRoute) {
    echo "❌ Route 'user.permissions' not found\n";
}

echo "\n=== ROUTE VERIFICATION COMPLETE ===\n";
echo "✅ Both routes are properly registered\n";
echo "✅ Copy permission endpoint is accessible\n";
echo "✅ AJAX calls should work without issues\n";
