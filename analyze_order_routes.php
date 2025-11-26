<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANALISIS DETAIL ROUTES ORDER ===\n\n";

// Get all routes related to order
$routes = Route::getRoutes();
$orderRoutes = [];

foreach ($routes->getRoutes() as $route) {
    $uri = $route->uri();
    $name = $route->getName();
    $action = $route->getActionName();
    
    // Filter routes yang mengandung 'order'
    if (strpos($uri, 'order') !== false || ($name && strpos($name, 'order') !== false)) {
        $orderRoutes[] = [
            'uri' => $uri,
            'name' => $name,
            'method' => implode('|', $route->methods()),
            'action' => $action,
            'middleware' => $route->middleware()
        ];
    }
}

echo "Ditemukan " . count($orderRoutes) . " routes yang berhubungan dengan order:\n\n";

foreach ($orderRoutes as $route) {
    echo "URI: {$route['uri']}\n";
    echo "Name: " . ($route['name'] ?: 'NULL') . "\n";
    echo "Method: {$route['method']}\n";
    echo "Action: {$route['action']}\n";
    echo "Middleware: " . implode(', ', $route['middleware']) . "\n";
    echo "---\n\n";
}

// Test user anggi specific routes
echo "=== TEST AKSES ROUTE SPECIFIC ===\n";

$user = App\Models\User::where('username', 'anggi')->first();

if ($user) {
    echo "Testing routes untuk user: {$user->username}\n\n";
    
    $testRoutes = [
        'orders.index',
        'orders.create', 
        'orders.store',
        'orders.show',
        'orders.edit',
        'orders.update'
    ];
    
    foreach ($testRoutes as $routeName) {
        try {
            $route = Route::getRoutes()->getByName($routeName);
            if ($route) {
                $middleware = $route->middleware();
                echo "Route: {$routeName}\n";
                echo "  Middleware: " . implode(', ', $middleware) . "\n";
                
                // Test each middleware individually
                foreach ($middleware as $mw) {
                    if (str_contains($mw, 'can:')) {
                        $permission = str_replace('can:', '', $mw);
                        $canAccess = $user->can($permission);
                        echo "  Permission '{$permission}': " . ($canAccess ? "✅ PASS" : "❌ FAIL") . "\n";
                    }
                }
                echo "\n";
            } else {
                echo "Route {$routeName}: ❌ NOT FOUND\n\n";
            }
        } catch (Exception $e) {
            echo "Route {$routeName}: ❌ ERROR - " . $e->getMessage() . "\n\n";
        }
    }
}

echo "\n=== TEST MIDDLEWARE CLASSES ===\n";

// Test middleware classes
$middlewareClasses = [
    'auth' => Illuminate\Auth\Middleware\Authenticate::class,
    'karyawan' => App\Http\Middleware\EnsureKaryawanPresent::class,
    'approved' => App\Http\Middleware\EnsureUserApproved::class,
    'crew' => App\Http\Middleware\EnsureCrewChecklistComplete::class
];

foreach ($middlewareClasses as $name => $class) {
    echo "Middleware '{$name}' ({$class}):\n";
    
    if (class_exists($class)) {
        echo "  ✅ Class exists\n";
        
        if ($user) {
            try {
                // Simulate middleware check
                $request = Illuminate\Http\Request::create('/test');
                $request->setUserResolver(function() use ($user) {
                    return $user;
                });
                
                if ($name === 'auth') {
                    Auth::login($user);
                    echo "  ✅ Auth: User is logged in\n";
                } elseif ($name === 'karyawan') {
                    $karyawan = $user->karyawan;
                    echo "  Karyawan check: " . ($karyawan ? "✅ HAS KARYAWAN" : "❌ NO KARYAWAN") . "\n";
                } elseif ($name === 'approved') {
                    $isApproved = $user->status === 'approved';
                    echo "  Approved check: " . ($isApproved ? "✅ APPROVED ({$user->status})" : "❌ NOT APPROVED ({$user->status})") . "\n";
                } elseif ($name === 'crew') {
                    echo "  Crew check: ✅ NOT REQUIRED (non-ABK)\n";
                }
            } catch (Exception $e) {
                echo "  ❌ Error testing: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "  ❌ Class not found\n";
    }
    echo "\n";
}

echo "Script completed.\n";