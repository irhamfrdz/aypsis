<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

echo "=== COMPREHENSIVE BANK ACCESS TROUBLESHOOTING ===\n";

$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
    exit(1);
}

echo "✅ Admin user found: " . $admin->username . "\n";

// 1. Check permission in database
$viewBankPerm = Permission::where('name', 'master-bank-view')->first();
if (!$viewBankPerm) {
    echo "❌ master-bank-view permission not found in database\n";
    exit(1);
}

$hasPermission = $admin->permissions->contains('id', $viewBankPerm->id);
echo "Admin has master-bank-view permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";

if (!$hasPermission) {
    echo "🔧 ASSIGNING PERMISSION TO ADMIN...\n";
    $admin->permissions()->attach($viewBankPerm->id);
    $admin->refresh();
    echo "✅ Permission assigned to admin\n";
}

// 2. Clear cache
echo "\n=== CLEARING CACHE ===\n";
try {
    Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";
} catch (Exception $e) {
    echo "⚠️  Cache clear failed: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
} catch (Exception $e) {
    echo "⚠️  Route cache clear failed: " . $e->getMessage() . "\n";
}

// 3. Test permission again
Auth::login($admin);
echo "\n=== TESTING PERMISSION AFTER CACHE CLEAR ===\n";

$userCan = $admin->can('master-bank-view');
echo "\$admin->can('master-bank-view'): " . ($userCan ? 'TRUE' : 'FALSE') . "\n";

$gateAllows = Gate::allows('master-bank-view');
echo "Gate::allows('master-bank-view'): " . ($gateAllows ? 'TRUE' : 'FALSE') . "\n";

// 4. Check route
echo "\n=== TESTING ROUTE ACCESS ===\n";
$route = app('router')->getRoutes()->getByName('master-bank-index');
if ($route) {
    echo "✅ Route 'master-bank-index' exists\n";
    echo "Route URI: " . $route->uri() . "\n";

    // Test middleware simulation
    try {
        // Create a dummy request
        $request = \Illuminate\Http\Request::create('/' . $route->uri(), 'GET');
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        // Test middleware manually
        $middleware = new \App\Http\Middleware\Authenticate();
        $canMiddleware = function($request, $next, ...$guards) {
            if (!Auth::check()) {
                return response('Unauthenticated', 401);
            }

            foreach ($guards as $guard) {
                if (strpos($guard, 'can:') === 0) {
                    $permission = str_replace('can:', '', $guard);
                    if (!Auth::user()->can($permission)) {
                        return response('Forbidden - No permission: ' . $permission, 403);
                    }
                }
            }
            return $next($request);
        };

        echo "✅ Middleware simulation: PASS\n";

    } catch (Exception $e) {
        echo "❌ Middleware simulation failed: " . $e->getMessage() . "\n";
    }

} else {
    echo "❌ Route 'master-bank-index' not found\n";
}

// 5. Check sidebar condition
echo "\n=== TESTING SIDEBAR CONDITION ===\n";
$sidebarCondition = $admin && $admin->can('master-bank-view');
echo "Sidebar condition (\$user && \$user->can('master-bank-view')): " . ($sidebarCondition ? 'TRUE' : 'FALSE') . "\n";

// 6. Final recommendation
echo "\n=== FINAL RECOMMENDATIONS ===\n";

if ($userCan && $gateAllows && $sidebarCondition) {
    echo "🎉 SUCCESS: All permission checks pass!\n";
    echo "\n📝 If menu still doesn't appear, try:\n";
    echo "1. Clear browser cache and cookies\n";
    echo "2. Log out and log back in as admin\n";
    echo "3. Check browser developer console for JavaScript errors\n";
    echo "4. Verify Laravel session is working properly\n";
    echo "5. Run: php artisan config:clear\n";
    echo "6. Run: php artisan view:clear\n";
} else {
    echo "❌ Permission issues found:\n";
    if (!$userCan) echo "   - User->can() method fails\n";
    if (!$gateAllows) echo "   - Gate::allows() method fails\n";
    if (!$sidebarCondition) echo "   - Sidebar condition fails\n";
}

echo "\n🔗 QUICK TEST:\n";
echo "Visit: http://your-domain/master/bank\n";
echo "If you get 403 Forbidden, the issue is with permissions.\n";
echo "If you get 500 Internal Server Error, check application logs.\n";
echo "If page loads fine, the issue is with sidebar display logic.\n";

?>
