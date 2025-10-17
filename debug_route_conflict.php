<?php

use Illuminate\Support\Facades\Route;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING ROUTE KONFLIK ===" . PHP_EOL;

$router = app('router');
$routes = $router->getRoutes();

echo "Semua route dengan prefix 'approval':" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'approval') === 0) {
        $name = $route->getName() ?: '(no name)';
        $methods = implode('|', $route->methods());
        $action = $route->getActionName();
        $middleware = implode(', ', $route->gatherMiddleware());

        echo "URI: $uri" . PHP_EOL;
        echo "Name: $name" . PHP_EOL;
        echo "Methods: $methods" . PHP_EOL;
        echo "Action: $action" . PHP_EOL;
        echo "Middleware: $middleware" . PHP_EOL;
        echo str_repeat("-", 80) . PHP_EOL;
    }
}

echo PHP_EOL . "Cek khusus route 'approval/surat-jalan':" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

// Test matching route
$request = \Illuminate\Http\Request::create('/approval/surat-jalan', 'GET');
try {
    $route = $router->getRoutes()->match($request);
    echo "✅ Route DITEMUKAN!" . PHP_EOL;
    echo "Matched Route: " . $route->getName() . PHP_EOL;
    echo "Action: " . $route->getActionName() . PHP_EOL;
    echo "Middleware: " . implode(', ', $route->gatherMiddleware()) . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Route TIDAK DITEMUKAN!" . PHP_EOL;
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== END DEBUG ===" . PHP_EOL;
