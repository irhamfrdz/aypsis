<?php

// Test simple route access
use Illuminate\Support\Facades\Route;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ROUTE DEBUG INFO ===" . PHP_EOL;

// Check if route exists
$router = app('router');
$routes = $router->getRoutes();

echo "Looking for approval.surat-jalan.index route..." . PHP_EOL;

foreach ($routes as $route) {
    if (str_contains($route->getName() ?? '', 'approval.surat-jalan')) {
        echo "Found route: " . $route->getName() . PHP_EOL;
        echo "URI: " . $route->uri() . PHP_EOL;
        echo "Methods: " . implode(', ', $route->methods()) . PHP_EOL;
        echo "Action: " . $route->getActionName() . PHP_EOL;
        echo "Middleware: " . implode(', ', $route->gatherMiddleware()) . PHP_EOL;
        echo "---" . PHP_EOL;
    }
}

// Test URL generation
try {
    $url = route('approval.surat-jalan.index');
    echo "Generated URL: " . $url . PHP_EOL;
} catch (Exception $e) {
    echo "Failed to generate URL: " . $e->getMessage() . PHP_EOL;
}

// Test controller instantiation
try {
    $controller = new \App\Http\Controllers\SuratJalanApprovalController();
    echo "Controller instantiated successfully" . PHP_EOL;
} catch (Exception $e) {
    echo "Failed to instantiate controller: " . $e->getMessage() . PHP_EOL;
}

echo "=== END DEBUG ===" . PHP_EOL;
