<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== CHECKING UNPROTECTED ROUTES ===\n\n";

$routes = Route::getRoutes();
$unprotectedRoutes = [];
$protectedRoutes = [];

foreach ($routes as $route) {
    $middleware = $route->middleware();
    $name = $route->getName();
    $methods = implode('|', $route->methods());
    $uri = $route->uri();

    // Skip routes that don't need protection
    $skipPatterns = [
        'login', 'logout', 'register', 'password', 'storage',
        'api/', '_debugbar', 'up', 'telescope', 'test-perm'
    ];

    $shouldSkip = false;
    foreach ($skipPatterns as $pattern) {
        if (strpos($uri, $pattern) !== false || strpos($name ?? '', $pattern) !== false) {
            $shouldSkip = true;
            break;
        }
    }

    if ($shouldSkip) continue;

    // Check if route has auth and permission middleware
    $hasAuth = in_array('auth', $middleware);
    $hasPermission = false;

    foreach ($middleware as $m) {
        if (strpos($m, 'can:') === 0) {
            $hasPermission = true;
            break;
        }
    }

    if ($hasAuth && $hasPermission) {
        $protectedRoutes[] = [
            'name' => $name,
            'uri' => $uri,
            'methods' => $methods,
            'middleware' => $middleware
        ];
    } elseif (!$hasAuth || !$hasPermission) {
        $unprotectedRoutes[] = [
            'name' => $name,
            'uri' => $uri,
            'methods' => $methods,
            'middleware' => $middleware,
            'has_auth' => $hasAuth,
            'has_permission' => $hasPermission
        ];
    }
}

echo "🔒 PROTECTED ROUTES: " . count($protectedRoutes) . "\n";
echo "⚠️  UNPROTECTED ROUTES: " . count($unprotectedRoutes) . "\n\n";

if (count($unprotectedRoutes) > 0) {
    echo "=== UNPROTECTED ROUTES THAT NEED ATTENTION ===\n";
    foreach ($unprotectedRoutes as $route) {
        echo "❌ {$route['methods']} {$route['uri']} ({$route['name']})\n";
        echo "   Auth: " . ($route['has_auth'] ? '✅' : '❌') . " | Permission: " . ($route['has_permission'] ? '✅' : '❌') . "\n";
        echo "   Middleware: " . implode(', ', $route['middleware']) . "\n\n";
    }
}

echo "=== SUMMARY ===\n";
echo "Total routes checked: " . (count($protectedRoutes) + count($unprotectedRoutes)) . "\n";
echo "Protection coverage: " . round((count($protectedRoutes) / (count($protectedRoutes) + count($unprotectedRoutes))) * 100, 1) . "%\n";
