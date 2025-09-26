<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing route resolution...\n";

try {
    // Test route resolution
    $url = route('master-coa-download-template');
    echo "Route URL: $url\n";

    // Get all routes
    $routes = $app->router->getRoutes();

    echo "Total routes loaded: " . count($routes) . "\n\n";

    // Test controller instantiation
    $controller = app(\App\Http\Controllers\MasterCoaController::class);
    echo "Controller instantiated successfully\n";

    // Test method call
    $response = $controller->downloadTemplate();
    echo "Method executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
    echo "Response status: " . $response->getStatusCode() . "\n";

    // Find master routes
    $masterRoutes = [];
    $found = false;

    foreach ($routes as $route) {
        $name = $route->getName();

        if ($name === 'master-divisi-index') {
            $found = true;
            echo "✅ Route 'master-divisi-index' found!\n";
            echo "URI: " . $route->uri() . "\n";
            echo "Methods: " . implode(', ', $route->methods()) . "\n";
        }

        if (strpos($name, 'master-') === 0) {
            $masterRoutes[] = $name;
        }
    }

    if (!$found) {
        echo "❌ Route 'master-divisi-index' not found\n";
    }

    echo "\n=== Master routes found ===\n";
    if (empty($masterRoutes)) {
        echo "No master routes found!\n";
    } else {
        foreach (array_slice($masterRoutes, 0, 10) as $route) {
            echo "- " . $route . "\n";
        }
        if (count($masterRoutes) > 10) {
            echo "... and " . (count($masterRoutes) - 10) . " more\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
