<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$user = User::where('username', 'admin')->first();
if (!$user) {
    echo 'Admin user not found!' . PHP_EOL;
    exit;
}

Auth::login($user);

echo 'Testing route access...' . PHP_EOL;

try {
    // Test route resolution
    $route = Route::getRoutes()->getByName('master.kode-nomor.index');
    if ($route) {
        echo '✅ Route exists: ' . $route->uri() . PHP_EOL;

        // Test middleware
        $middleware = $route->middleware();
        echo 'Middleware: ' . implode(', ', $middleware) . PHP_EOL;

        // Test permission check
        $canAccess = $user->can('master-kode-nomor-view');
        echo 'User can access: ' . ($canAccess ? 'YES' : 'NO') . PHP_EOL;

        if ($canAccess) {
            echo '🎉 Route should be accessible!' . PHP_EOL;
        } else {
            echo '❌ Permission denied' . PHP_EOL;
        }
    } else {
        echo '❌ Route not found' . PHP_EOL;
    }
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . PHP_EOL;
}
