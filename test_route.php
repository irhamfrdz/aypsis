<?php

// Simple test to check if routes are loaded
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    $routes = $app->router->getRoutes();

    echo "Total routes loaded: " . count($routes) . "\n\n";

    $found = false;
    $masterRoutes = [];
    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name === 'master.divisi.index') {
            $found = true;
            echo "✅ Route 'master.divisi.index' found!\n";
            echo "URI: " . $route->uri() . "\n";
            echo "Methods: " . implode(', ', $route->methods()) . "\n";
        }
        if (strpos($name, 'master.') === 0) {
            $masterRoutes[] = $name;
        }
    }

    if (!$found) {
        echo "❌ Route 'master.divisi.index' not found\n";
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
}
