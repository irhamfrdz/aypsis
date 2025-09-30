<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing route access for perbaikan-kontainer.index...\n";

// Find user marlina
$user = User::where('username', 'marlina')->first();

if (!$user) {
    echo "User marlina not found!\n";
    exit(1);
}

echo "User found: {$user->username} (ID: {$user->id})\n";

// Check permission
$canAccess = $user->can('tagihan-perbaikan-kontainer-view');
echo "Can access tagihan-perbaikan-kontainer-view: " . ($canAccess ? 'YES' : 'NO') . "\n";

// Try to check if route exists and middleware
$route = Route::getRoutes()->getByName('perbaikan-kontainer.index');
if ($route) {
    echo "Route found: perbaikan-kontainer.index\n";
    $middleware = $route->middleware();
    echo "Middleware: " . implode(', ', $middleware) . "\n";

    // Check if user passes the middleware
    $request = Request::create('/perbaikan-kontainer', 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    // Simulate middleware check
    $passes = true;
    foreach ($middleware as $mw) {
        if (strpos($mw, 'can:') === 0) {
            $permission = str_replace('can:', '', $mw);
            if (!$user->can($permission)) {
                $passes = false;
                echo "FAIL: User does not have permission '$permission'\n";
                break;
            }
        }
    }

    if ($passes) {
        echo "SUCCESS: User should be able to access the route\n";
    } else {
        echo "FAIL: User cannot access the route due to middleware\n";
    }
} else {
    echo "Route not found!\n";
}

echo "Route test completed.\n";
