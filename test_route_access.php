<?php<?php<?php



require_once 'vendor/autoload.php';



use Illuminate\Http\Request;require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use Illuminate\Http\Request;// Bootstrap Laravel

/**

 * Test Route Access with Permission Details Middlewareuse Illuminate\Support\Facades\Route;$app = require_once 'bootstrap/app.php';

 */

use Illuminate\Support\Facades\Auth;$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Route Access with permission.details Middleware\n";

echo "=========================================================\n\n";use App\Models\User;



// Bootstrap Laraveluse Illuminate\Support\Facades\Auth;

$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();/**use App\Models\User;



// Get first user * Test Route Access with Permission Details Middleware

$user = User::first();

if (!$user) { */echo "🧪 Testing Route Access for User test2\n";

    echo "❌ No users found\n";

    exit(1);echo "=====================================\n\n";

}

echo "🧪 Testing Route Access with permission.details Middleware\n";

echo "Using user: {$user->name}\n\n";

echo "=========================================================\n\n";// Login as user test2

// Simulate authentication

Auth::login($user);$user = User::where('username', 'test2')->first();

echo "User authenticated: ✅\n\n";

// Bootstrap Laravel

// Test direct route access

echo "Testing route access...\n";$app = require_once 'bootstrap/app.php';if (!$user) {



try {$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();    echo "❌ User test2 not found\n";

    // Create a test request to a master karyawan route

    $request = Request::create('/master/karyawan', 'GET');    exit(1);



    // Get the router and resolve the route// Get first user}

    $router = app('router');

    $routes = $router->getRoutes();$user = User::first();



    // Find the routeif (!$user) {Auth::login($user);

    $route = null;

    foreach ($routes as $r) {    echo "❌ No users found\n";echo "✅ Logged in as: {$user->username}\n\n";

        if ($r->uri() === 'master/karyawan' && in_array('GET', $r->methods())) {

            $route = $r;    exit(1);

            break;

        }}// Test route access

    }

echo "🚪 Testing route access:\n";

    if ($route) {

        echo "Route found: ✅\n";echo "Using user: {$user->name}\n\n";

        echo "Route middleware: " . json_encode($route->middleware()) . "\n";

try {

        // Test middleware resolution

        $middlewareStack = $route->middleware();// Simulate authentication    // Create a request to test the route

        foreach ($middlewareStack as $middleware) {

            if (str_starts_with($middleware, 'permission.details:')) {Auth::login($user);    $request = \Illuminate\Http\Request::create('/pranota-supir', 'GET');

                echo "Found permission.details middleware: ✅\n";

                $permission = str_replace('permission.details:', '', $middleware);echo "User authenticated: ✅\n\n";

                echo "Required permission: {$permission}\n";

                break;    // Get the router and dispatch the request

            }

        }// Test direct route access    $router = app('router');

    } else {

        echo "Route not found: ❌\n";echo "Testing route access...\n";    $response = $router->dispatch($request);

    }



} catch (Exception $e) {

    echo "Error during route test: {$e->getMessage()}\n";try {    $statusCode = $response->getStatusCode();

}

    // Create a test request to a master karyawan route    $statusText = $statusCode === 200 ? '✅ SUCCESS' : '❌ FAILED';

echo "\n✅ Route access test completed!\n";
    $request = Request::create('/master/karyawan', 'GET');

    echo "  GET /pranota-supir: {$statusText} (Status: {$statusCode})\n";

    // Get the router and resolve the route

    $router = app('router');    if ($statusCode === 403) {

    $routes = $router->getRoutes();        echo "  ❌ Still getting 403 Forbidden - middleware not working properly\n";

    } elseif ($statusCode === 200) {

    // Find the route        echo "  ✅ Route accessible - middleware working correctly!\n";

    $route = null;    } else {

    foreach ($routes as $r) {        echo "  ⚠️ Unexpected status code: {$statusCode}\n";

        if ($r->uri() === 'master/karyawan' && in_array('GET', $r->methods())) {    }

            $route = $r;

            break;} catch (Exception $e) {

        }    echo "  ❌ Exception during route test: " . $e->getMessage() . "\n";

    }}



    if ($route) {echo "\n🎉 Route access test completed!\n";

        echo "Route found: ✅\n";
        echo "Route middleware: " . json_encode($route->middleware()) . "\n";

        // Test middleware resolution
        $middlewareStack = $route->middleware();
        foreach ($middlewareStack as $middleware) {
            if (str_starts_with($middleware, 'permission.details:')) {
                echo "Found permission.details middleware: ✅\n";
                $permission = str_replace('permission.details:', '', $middleware);
                echo "Required permission: {$permission}\n";
                break;
            }
        }
    } else {
        echo "Route not found: ❌\n";
    }

} catch (Exception $e) {
    echo "Error during route test: {$e->getMessage()}\n";
}

echo "\n✅ Route access test completed!\n";
