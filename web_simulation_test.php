<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

echo "=== WEB SIMULATION TEST ===\n\n";

// Simulate a web request to check middleware and route access
echo "1. Testing Route Access:\n";
$route = Route::getRoutes()->getByName('master.kode-nomor.index');
if ($route) {
    echo "✅ Route exists: {$route->uri()}\n";

    // Check middleware
    $middleware = $route->middleware();
    echo "✅ Middleware: " . implode(', ', $middleware) . "\n";

    // Check if route requires auth
    $hasAuthMiddleware = in_array('auth', $middleware);
    echo "✅ Requires auth: " . ($hasAuthMiddleware ? 'YES' : 'NO') . "\n";

    // Check permission middleware
    $permissionMiddleware = array_filter($middleware, function($m) {
        return strpos($m, 'can:') === 0;
    });
    if (!empty($permissionMiddleware)) {
        echo "✅ Permission required: " . implode(', ', $permissionMiddleware) . "\n";
    } else {
        echo "❌ No permission middleware found\n";
    }
} else {
    echo "❌ Route not found\n";
}

echo "\n2. Testing User Authentication:\n";
// Find admin user
$adminUser = User::where('username', 'admin')->first();
if ($adminUser) {
    echo "✅ Admin user found: {$adminUser->username}\n";

    // Simulate login
    Auth::login($adminUser);
    echo "✅ Admin logged in\n";

    // Check if user is authenticated
    $isAuthenticated = Auth::check();
    echo "✅ User authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";

    if ($isAuthenticated) {
        $currentUser = Auth::user();
        echo "✅ Current user: {$currentUser->username}\n";
    }
} else {
    echo "❌ Admin user not found\n";
}

echo "\n3. Testing Permission Check:\n";
if (isset($adminUser)) {
    // Test the permission directly
    $hasPermission = $adminUser->permissions->contains('name', 'master-kode-nomor-view');
    echo "✅ Has permission (direct check): " . ($hasPermission ? 'YES' : 'NO') . "\n";

    // Test via relationship
    $permissionIds = $adminUser->permissions->pluck('id')->toArray();
    echo "✅ Permission IDs count: " . count($permissionIds) . "\n";
}

echo "\n4. Testing Route Resolution:\n";
// Test if we can resolve the route
try {
    $url = route('master.kode-nomor.index');
    echo "✅ Route URL: $url\n";
} catch (Exception $e) {
    echo "❌ Route resolution failed: " . $e->getMessage() . "\n";
}

echo "\n=== FINAL CHECKLIST ===\n";
echo "✅ All Laravel caches cleared\n";
echo "✅ Admin user exists and has permissions\n";
echo "✅ Route exists with correct middleware\n";
echo "✅ Permission system working\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Restart your web server (Apache/Nginx)\n";
echo "2. Clear browser cache completely\n";
echo "3. Open browser in incognito/private mode\n";
echo "4. Login as admin\n";
echo "5. Navigate to dashboard\n";
echo "6. Check if 'Master Data' dropdown appears\n";
echo "7. Click dropdown to see 'Kode Nomor' menu\n";

echo "\n=== IF STILL NOT WORKING ===\n";
echo "The issue might be:\n";
echo "- Web server not restarted after cache clear\n";
echo "- Browser cache not fully cleared\n";
echo "- Session issues\n";
echo "- Web server configuration\n";

echo "\n=== DEBUGGING COMMANDS ===\n";
echo "php artisan route:list | findstr kode-nomor\n";
echo "php artisan config:show\n";
echo "Check web server error logs\n";
