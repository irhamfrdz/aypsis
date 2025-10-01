<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SERVER vs LOCAL PERMISSION DEBUG ===" . PHP_EOL;

// Test dengan user yang sebenarnya mengalami masalah
echo "Masukkan username yang mengalami masalah (tekan Enter untuk admin): ";
$handle = fopen("php://stdin", "r");
$username = trim(fgets($handle));
fclose($handle);

if (empty($username)) {
    $username = 'admin';
}

$user = \App\Models\User::where('username', $username)->first();
if (!$user) {
    echo "❌ User '$username' tidak ditemukan" . PHP_EOL;
    exit;
}

echo "👤 Testing user: " . $user->username . " (ID: " . $user->id . ")" . PHP_EOL;

// Cek middleware yang dijalankan oleh route
echo PHP_EOL . "🛡️ DETAILED MIDDLEWARE ANALYSIS:" . PHP_EOL;

$router = app('router');
$routes = $router->getRoutes();

$bankRoutes = [
    'master-bank-index',
    'master-bank-create',
    'master-bank-show',
    'master-bank-edit',
    'master-bank-store',
    'master-bank-update',
    'master-bank-destroy'
];

foreach ($bankRoutes as $routeName) {
    echo PHP_EOL . "🔍 Route: $routeName" . PHP_EOL;

    try {
        $route = $routes->getByName($routeName);
        if ($route) {
            echo "   ✅ Route exists" . PHP_EOL;
            echo "   📍 URI: " . $route->uri() . PHP_EOL;
            echo "   🎯 Methods: " . implode(', ', $route->methods()) . PHP_EOL;

            $middleware = $route->middleware();
            echo "   🛡️ Middleware count: " . count($middleware) . PHP_EOL;

            // Group middleware by type
            $authMiddleware = [];
            $canMiddleware = [];
            $customMiddleware = [];

            foreach ($middleware as $mw) {
                if ($mw === 'auth') {
                    $authMiddleware[] = $mw;
                } elseif (str_starts_with($mw, 'can:')) {
                    $canMiddleware[] = $mw;
                } else {
                    $customMiddleware[] = $mw;
                }
            }

            if (!empty($authMiddleware)) {
                echo "   🔐 Auth: " . implode(', ', $authMiddleware) . PHP_EOL;
            }

            if (!empty($customMiddleware)) {
                echo "   ⚙️ Custom: " . implode(', ', $customMiddleware) . PHP_EOL;
            }

            if (!empty($canMiddleware)) {
                echo "   🔑 Permissions: " . implode(', ', $canMiddleware) . PHP_EOL;

                // Test each permission
                foreach ($canMiddleware as $canMW) {
                    $perm = str_replace('can:', '', $canMW);
                    $hasAccess = $user->can($perm);
                    echo "      - $perm: " . ($hasAccess ? "✅" : "❌") . PHP_EOL;
                }
            }
        } else {
            echo "   ❌ Route not found" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "🔍 PERMISSION VERIFICATION:" . PHP_EOL;

// Check specific permission yang sering bermasalah
$criticalPermissions = [
    'master-bank-view',
    'master-bank-index',
];

foreach ($criticalPermissions as $perm) {
    echo PHP_EOL . "Testing: $perm" . PHP_EOL;

    // Check if permission exists
    $permExists = \App\Models\Permission::where('name', $perm)->exists();
    echo "   Permission exists in DB: " . ($permExists ? "✅" : "❌") . PHP_EOL;

    // Check if user has permission
    $userHasPerm = $user->hasPermissionTo($perm);
    echo "   User has permission (hasPermissionTo): " . ($userHasPerm ? "✅" : "❌") . PHP_EOL;

    // Check with can() method
    $canAccess = $user->can($perm);
    echo "   User can access (can): " . ($canAccess ? "✅" : "❌") . PHP_EOL;

    // Check pivot table directly
    $pivotExists = \Illuminate\Support\Facades\DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $user->id)
        ->where('permissions.name', $perm)
        ->exists();
    echo "   Pivot table relationship: " . ($pivotExists ? "✅" : "❌") . PHP_EOL;
}

echo PHP_EOL . "🚨 POTENTIAL ISSUES CHECK:" . PHP_EOL;

// Check for common server issues
echo "1. Session Issues:" . PHP_EOL;
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "   ✅ Session is active" . PHP_EOL;
} else {
    echo "   ⚠️ Session not active" . PHP_EOL;
}

echo PHP_EOL . "2. Environment:" . PHP_EOL;
echo "   Environment: " . app()->environment() . PHP_EOL;
echo "   Debug mode: " . (config('app.debug') ? "ON" : "OFF") . PHP_EOL;

echo PHP_EOL . "3. Database Connection:" . PHP_EOL;
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "   ✅ Database connected" . PHP_EOL;
} catch (Exception $e) {
    echo "   ❌ Database connection error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "💡 TROUBLESHOOTING STEPS FOR SERVER:" . PHP_EOL;
echo "1. Clear browser cache and cookies" . PHP_EOL;
echo "2. Log out and log back in" . PHP_EOL;
echo "3. Check server logs for specific error messages" . PHP_EOL;
echo "4. Verify .env configuration matches local" . PHP_EOL;
echo "5. Check file permissions on server" . PHP_EOL;
echo "6. Restart web server (nginx/apache) and PHP-FPM" . PHP_EOL;

?>
