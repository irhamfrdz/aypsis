<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo '❌ User test4 not found' . PHP_EOL;
    exit;
}

echo '=== TESTING HTTP ROUTE ACCESS ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

// Simulate authenticated request
Auth::login($user);

$testRoutes = [
    ['method' => 'GET', 'uri' => '/permohonan', 'name' => 'permohonan.index'],
    ['method' => 'GET', 'uri' => '/permohonan/create', 'name' => 'permohonan.create'],
];

foreach ($testRoutes as $route) {
    try {
        // Create a request instance
        $request = Request::create($route['uri'], $route['method']);

        // Get the route
        $laravelRoute = app('router')->getRoutes()->match($request);

        // Check if route exists
        if ($laravelRoute) {
            echo $route['name'] . ' (' . $route['method'] . ' ' . $route['uri'] . '): ✅ Route exists' . PHP_EOL;

            // Check middleware
            $middleware = $laravelRoute->middleware();
            if (in_array('can:permohonan.index', $middleware) || in_array('can:permohonan.create', $middleware)) {
                echo '  - Has permission middleware: ✅' . PHP_EOL;
            } else {
                echo '  - Has permission middleware: ❌' . PHP_EOL;
            }
        } else {
            echo $route['name'] . ' (' . $route['method'] . ' ' . $route['uri'] . '): ❌ Route not found' . PHP_EOL;
        }
    } catch (Exception $e) {
        echo $route['name'] . ' (' . $route['method'] . ' ' . $route['uri'] . '): ❌ Error - ' . $e->getMessage() . PHP_EOL;
    }
    echo PHP_EOL;
}

Auth::logout();

echo '=== FINAL VERIFICATION ===' . PHP_EOL;
echo '✅ Route permohonan.index should be accessible for user test4' . PHP_EOL;
echo '❌ Route permohonan.create should be blocked for user test4' . PHP_EOL;
echo PHP_EOL;
echo 'If user test4 can still access /permohonan/create, there might be:' . PHP_EOL;
echo '1. Browser cache issues' . PHP_EOL;
echo '2. Admin override in the application' . PHP_EOL;
echo '3. Different permission checking in the controller' . PHP_EOL;
