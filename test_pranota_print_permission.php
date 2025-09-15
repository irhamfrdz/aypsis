<?php

// Test script to verify pranota print permission enforcement
// This script simulates user test4 trying to access pranota print functionality

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pranota;

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test: Pranota Print Permission Enforcement\n";
echo "==========================================\n\n";

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

// Test 2: Simulate accessing pranota print route
echo "\nTest 2: Simulating pranota print route access\n";

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

        // Check if permission-like middleware is present
        $hasPermissionMiddleware = false;
        foreach ($middleware as $mw) {
            if (strpos($mw, 'permission-like') !== false) {
                $hasPermissionMiddleware = true;
                break;
            }
        }

        if ($hasPermissionMiddleware) {
            echo "✅ Permission middleware found\n";

            // Test if user has the required permission
            if ($user->hasPermissionTo('pranota.print')) {
                echo "✅ User test4 HAS pranota.print permission - should be able to access\n";
            } else {
                echo "❌ User test4 does NOT have pranota.print permission - should be blocked\n";
                echo "🔒 This is the expected behavior after the fix\n";
            }
        } else {
            echo "❌ FAIL: No permission-like middleware found\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR during route test: " . $e->getMessage() . "\n";
}

// Test 3: Check pembayaran-pranota-kontainer print route
echo "\nTest 3: Checking pembayaran-pranota-kontainer print route\n";

try {
    $pembayaranRoutes = Route::getRoutes();
    $pembayaranPrintRoute = null;

    foreach ($pembayaranRoutes as $route) {
        if ($route->getName() === 'pembayaran-pranota-kontainer.print') {
            $pembayaranPrintRoute = $route;
            break;
        }
    }

    if (!$pembayaranPrintRoute) {
        echo "❌ FAIL: pembayaran-pranota-kontainer.print route not found\n";
    } else {
        echo "✅ pembayaran-pranota-kontainer.print route found\n";

        $middleware = $pembayaranPrintRoute->middleware();
        echo "Route middleware: " . (empty($middleware) ? 'NONE' : implode(', ', $middleware)) . "\n";

        if (empty($middleware)) {
            echo "❌ FAIL: No middleware found on pembayaran-pranota-kontainer.print route\n";
        } else {
            echo "✅ Route has middleware protection\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR during pembayaran route test: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 SUMMARY:\n";
echo "- User test4 " . ($user->hasPermissionTo('pranota.print') ? 'HAS' : 'does NOT have') . " pranota.print permission\n";
echo "- pranota.print route " . (empty($middleware) ? 'has NO middleware' : 'has middleware protection') . "\n";
echo "- pembayaran-pranota-kontainer.print route " . (empty($middleware) ? 'has NO middleware' : 'has middleware protection') . "\n";

if (!$user->hasPermissionTo('pranota.print') && !empty($middleware)) {
    echo "\n✅ SUCCESS: Permission system should now properly block user test4 from printing pranota\n";
} else {
    echo "\n❌ ISSUE: There may still be permission bypass issues\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
