<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo '❌ User test4 not found' . PHP_EOL;
    exit;
}

echo '=== CHECKING USER TEST4 PERMISSIONS ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

echo 'Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION CHECKS ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan"): ' . ($user->hasPermissionTo('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.index"): ' . ($user->hasPermissionTo('permohonan.index') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.create"): ' . ($user->hasPermissionTo('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.edit"): ' . ($user->hasPermissionTo('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.delete"): ' . ($user->hasPermissionTo('permohonan.delete') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo '=== GATE CHECKS ===' . PHP_EOL;
Auth::login($user);
echo 'Gate::allows("permohonan"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.index"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.index') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.create"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.edit"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.delete"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.delete') ? '✅ YES' : '❌ NO') . PHP_EOL;
Auth::logout();

echo PHP_EOL;
echo '=== ROUTE VERIFICATION ===' . PHP_EOL;
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'permohonan') && str_contains($route->uri(), 'edit')) {
        $middleware = $route->middleware();
        echo 'Route: ' . $route->uri() . PHP_EOL;
        echo 'Methods: ' . implode(', ', $route->methods()) . PHP_EOL;
        echo 'Middleware: ' . implode(', ', $middleware) . PHP_EOL;

        // Check if there's a 'can:' middleware
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'can:')) {
                $requiredPermission = str_replace('can:', '', $mw);
                echo 'Required permission: ' . $requiredPermission . PHP_EOL;
                echo 'User has this permission: ' . ($user->hasPermissionTo($requiredPermission) ? '✅ YES' : '❌ NO') . PHP_EOL;
            }
        }
        echo PHP_EOL;
        break;
    }
}

echo '=== DIAGNOSIS ===' . PHP_EOL;
$hasEditPermission = $user->hasPermissionTo('permohonan.edit');
$routeExists = false;
$routePermission = '';

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'permohonan') && str_contains($route->uri(), 'edit')) {
        $routeExists = true;
        $middleware = $route->middleware();
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'can:')) {
                $routePermission = str_replace('can:', '', $mw);
                break;
            }
        }
        break;
    }
}

echo 'Route exists: ' . ($routeExists ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'User has edit permission: ' . ($hasEditPermission ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Route requires permission: ' . $routePermission . PHP_EOL;

if ($routeExists && $hasEditPermission) {
    echo PHP_EOL . '✅ TECHNICALLY: User should be able to access edit route' . PHP_EOL;
    echo 'Possible issues:' . PHP_EOL;
    echo '1. Browser cache - try clearing browser cache' . PHP_EOL;
    echo '2. Laravel cache - try clearing Laravel cache' . PHP_EOL;
    echo '3. Permission not properly saved to database' . PHP_EOL;
    echo '4. Blade template permission check issue' . PHP_EOL;
} elseif (!$routeExists) {
    echo PHP_EOL . '❌ CRITICAL: Edit route does not exist' . PHP_EOL;
} elseif (!$hasEditPermission) {
    echo PHP_EOL . '❌ CRITICAL: User missing edit permission' . PHP_EOL;
}
