<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG BANK ACCESS PROBLEM ON SERVER ===" . PHP_EOL;

// Get admin user for testing
$admin = \App\Models\User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found" . PHP_EOL;
    exit;
}

echo "👤 Testing user: " . $admin->username . PHP_EOL;
echo "🆔 User ID: " . $admin->id . PHP_EOL;

// Check all bank-related permissions in database
echo PHP_EOL . "📋 Available Bank Permissions in Database:" . PHP_EOL;
$bankPermissions = \App\Models\Permission::where('name', 'like', '%bank%')->get();
foreach ($bankPermissions as $perm) {
    echo "   - " . $perm->name . " (" . $perm->description . ")" . PHP_EOL;
}

echo PHP_EOL . "🔍 Admin User Bank Permissions:" . PHP_EOL;
$userBankPerms = $admin->permissions()->where('name', 'like', '%bank%')->get();
if ($userBankPerms->count() > 0) {
    foreach ($userBankPerms as $perm) {
        echo "   ✅ " . $perm->name . PHP_EOL;
    }
} else {
    echo "   ❌ No bank permissions found for admin user" . PHP_EOL;
}

echo PHP_EOL . "🧪 Testing Permission Checks:" . PHP_EOL;
$testPerms = [
    'master-bank-view',
    'master-bank-index',
    'master-bank',
    'master-bank.view',
    'master-bank-create',
    'master-bank-update',
    'master-bank-delete'
];

foreach ($testPerms as $perm) {
    $canAccess = $admin->can($perm);
    echo "   " . $perm . ": " . ($canAccess ? "✅ YES" : "❌ NO") . PHP_EOL;
}

echo PHP_EOL . "🛣️ Route Analysis:" . PHP_EOL;
try {
    $route = app('router')->getRoutes()->getByName('master-bank-index');
    if ($route) {
        echo "   ✅ Route exists: master-bank-index" . PHP_EOL;
        echo "   📍 URI: " . $route->uri() . PHP_EOL;
        $middleware = $route->middleware();
        echo "   🛡️ Middleware: " . implode(', ', $middleware) . PHP_EOL;

        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'can:')) {
                $requiredPerm = str_replace('can:', '', $mw);
                echo "   🔑 Required permission: " . $requiredPerm . PHP_EOL;
                $hasIt = $admin->can($requiredPerm);
                echo "   🚪 Admin has it: " . ($hasIt ? "✅ YES" : "❌ NO") . PHP_EOL;
            }
        }
    } else {
        echo "   ❌ Route not found: master-bank-index" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "🔄 Cache Status Check:" . PHP_EOL;

// Check if there are cached views/config that might be causing issues
$configCached = file_exists(base_path('bootstrap/cache/config.php'));
$routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
$viewsCached = is_dir(storage_path('framework/views')) && count(glob(storage_path('framework/views/*.php'))) > 0;

echo "   Config cached: " . ($configCached ? "⚠️ YES" : "✅ NO") . PHP_EOL;
echo "   Routes cached: " . ($routesCached ? "⚠️ YES" : "✅ NO") . PHP_EOL;
echo "   Views cached: " . ($viewsCached ? "⚠️ YES" : "✅ NO") . PHP_EOL;

if ($configCached || $routesCached || $viewsCached) {
    echo PHP_EOL . "🚨 CACHE DETECTED! This might be the issue." . PHP_EOL;
    echo "   Run these commands:" . PHP_EOL;
    echo "   php artisan config:clear" . PHP_EOL;
    echo "   php artisan route:clear" . PHP_EOL;
    echo "   php artisan view:clear" . PHP_EOL;
    echo "   php artisan cache:clear" . PHP_EOL;
}

echo PHP_EOL . "🗄️ Database Connection Check:" . PHP_EOL;
try {
    $permCount = \App\Models\Permission::count();
    $userCount = \App\Models\User::count();
    echo "   ✅ Permissions in DB: " . $permCount . PHP_EOL;
    echo "   ✅ Users in DB: " . $userCount . PHP_EOL;
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "🔍 USER-PERMISSION PIVOT CHECK:" . PHP_EOL;
$pivotCount = \DB::table('user_permissions')->where('user_id', $admin->id)->count();
echo "   User " . $admin->username . " has " . $pivotCount . " permission relationships" . PHP_EOL;

$bankPivots = \DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $admin->id)
    ->where('permissions.name', 'like', '%bank%')
    ->select('permissions.name')
    ->get();

if ($bankPivots->count() > 0) {
    echo "   Bank permissions in pivot table:" . PHP_EOL;
    foreach ($bankPivots as $pivot) {
        echo "      - " . $pivot->name . PHP_EOL;
    }
} else {
    echo "   ❌ No bank permissions found in pivot table!" . PHP_EOL;
}

echo PHP_EOL . "💡 POSSIBLE SOLUTIONS:" . PHP_EOL;
echo "1. Clear all caches (config, route, view)" . PHP_EOL;
echo "2. Check if user actually has the right permissions in database" . PHP_EOL;
echo "3. Verify the route middleware matches the permission name" . PHP_EOL;
echo "4. Check if there are middleware conflicts" . PHP_EOL;
echo "5. Restart web server/php-fpm if using production server" . PHP_EOL;

?>
