<?php

// Test script to verify specific pranota print permission enforcement
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pranota;

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test: Specific Pranota Print Permission Enforcement\n";
echo "====================================================\n\n";

// Test 1: Check user test4 permissions
echo "Test 1: Checking user test4 permissions\n";
$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "❌ FAIL: User test4 not found\n";
    exit(1);
}

echo "✅ User test4 found (ID: {$user->id})\n";

// Check specific permissions
$permissions = [
    'pranota.view' => $user->hasPermissionTo('pranota.view'),
    'pranota.create' => $user->hasPermissionTo('pranota.create'),
    'pranota.update' => $user->hasPermissionTo('pranota.update'),
    'pranota.delete' => $user->hasPermissionTo('pranota.delete'),
    'pranota.print' => $user->hasPermissionTo('pranota.print'),
    'pranota.approve' => $user->hasPermissionTo('pranota.approve'),
];

echo "\nPranota permissions for test4:\n";
foreach ($permissions as $permission => $hasPermission) {
    $status = $hasPermission ? '✅ YES' : '❌ NO';
    echo "  {$permission}: {$status}\n";
}

// Test 2: Check permission-like vs specific permission
echo "\nTest 2: Comparing permission checks\n";

$hasPranotaLike = $user->hasPermissionLike('pranota');
$hasSpecificPrint = $user->hasPermissionTo('pranota.print');

echo "hasPermissionLike('pranota'): " . ($hasPranotaLike ? '✅ YES' : '❌ NO') . "\n";
echo "hasPermissionTo('pranota.print'): " . ($hasSpecificPrint ? '✅ YES' : '❌ NO') . "\n";

if ($hasPranotaLike && !$hasSpecificPrint) {
    echo "✅ This shows the difference - user has view permission but not print permission\n";
}

// Test 3: Simulate accessing pranota print route
echo "\nTest 3: Simulating pranota print route access\n";

// Get a sample pranota
$pranota = Pranota::first();
if (!$pranota) {
    echo "❌ FAIL: No pranota found in database\n";
    exit(1);
}

echo "✅ Sample pranota found (ID: {$pranota->id}, No: {$pranota->no_invoice})\n";

// Simulate authentication as test4
Auth::login($user);

// Test the route access
try {
    $request = Request::create("/pranota/{$pranota->id}/print", 'GET');

    // Check if route exists and has middleware
    $routes = Route::getRoutes();
    $printRoute = null;

    foreach ($routes as $route) {
        if ($route->getName() === 'pranota.print') {
            $printRoute = $route;
            break;
        }
    }

    if (!$printRoute) {
        echo "❌ FAIL: pranota.print route not found\n";
        exit(1);
    }

    echo "✅ pranota.print route found\n";

    // Check middleware
    $middleware = $printRoute->middleware();
    echo "Route middleware: " . (empty($middleware) ? 'NONE' : implode(', ', $middleware)) . "\n";

    if (empty($middleware)) {
        echo "❌ FAIL: No middleware found on pranota.print route\n";
        echo "🔧 This means the route is not protected!\n";
    } else {
        echo "✅ Route has middleware protection\n";

        // Check if specific permission middleware is present
        $hasPermissionMiddleware = false;
        foreach ($middleware as $mw) {
            if (strpos($mw, 'permission:pranota.print') !== false) {
                $hasPermissionMiddleware = true;
                break;
            }
        }

        if ($hasPermissionMiddleware) {
            echo "✅ Specific permission middleware found (permission:pranota.print)\n";

            // Test if user has the required permission
            if ($user->hasPermissionTo('pranota.print')) {
                echo "✅ User test4 HAS pranota.print permission - should be able to access\n";
            } else {
                echo "❌ User test4 does NOT have pranota.print permission - should be blocked\n";
                echo "🔒 This is the expected behavior after the fix\n";
            }
        } else {
            echo "❌ FAIL: No specific permission middleware found\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR during route test: " . $e->getMessage() . "\n";
}

// Test 4: Test middleware directly
echo "\nTest 4: Testing EnsurePermission middleware directly\n";

try {
    $middleware = new \App\Http\Middleware\EnsurePermission();

    // Create a mock request
    $request = Request::create("/pranota/{$pranota->id}/print", 'GET');

    // Test the middleware with specific permission
    $nextCalled = false;
    $next = function ($req) use (&$nextCalled) {
        $nextCalled = true;
        return response('OK');
    };

    try {
        $response = $middleware->handle($request, $next, 'pranota.print');
        if ($nextCalled) {
            echo "✅ Middleware ALLOWED access for pranota.print permission\n";
        } else {
            echo "❌ Middleware BLOCKED access for pranota.print permission\n";
        }
    } catch (Exception $e) {
        echo "❌ Middleware threw exception for pranota.print permission: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR during middleware test: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 SUMMARY:\n";
echo "- User test4 " . ($user->hasPermissionTo('pranota.print') ? 'HAS' : 'does NOT have') . " pranota.print permission\n";
echo "- hasPermissionLike('pranota'): " . ($hasPranotaLike ? 'YES' : 'NO') . "\n";
echo "- Route uses specific permission check: " . ($hasPermissionMiddleware ? 'YES' : 'NO') . "\n";

if (!$user->hasPermissionTo('pranota.print') && $hasPermissionMiddleware) {
    echo "\n✅ SUCCESS: Permission system should now properly block user test4 from printing pranota\n";
    echo "🎯 The fix ensures that only users with EXACT pranota.print permission can access the print feature\n";
} elseif ($user->hasPermissionTo('pranota.print')) {
    echo "\n⚠️ WARNING: User test4 has pranota.print permission - will be able to access print\n";
} else {
    echo "\n❌ ISSUE: Permission system may still have issues\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
