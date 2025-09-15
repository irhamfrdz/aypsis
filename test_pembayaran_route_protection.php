<?php

// Test script to verify pembayaran-pranota-kontainer route protection fix
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Route;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Pembayaran Pranota Kontainer Route Protection Fix\n";
echo "===========================================================\n\n";

$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "❌ FAIL: User test4 not found\n";
    exit(1);
}

echo "✅ User test4 found (ID: {$user->id})\n\n";

// Check user permissions
$permissions = [
    'view' => $user->hasPermissionTo('pembayaran-pranota-kontainer.view'),
    'create' => $user->hasPermissionTo('pembayaran-pranota-kontainer.create'),
    'update' => $user->hasPermissionTo('pembayaran-pranota-kontainer.update'),
    'delete' => $user->hasPermissionTo('pembayaran-pranota-kontainer.delete'),
    'print' => $user->hasPermissionTo('pembayaran-pranota-kontainer.print'),
];

echo "📋 User test4 permissions:\n";
foreach ($permissions as $action => $hasPermission) {
    $status = $hasPermission ? '✅ YES' : '❌ NO';
    echo "  pembayaran-pranota-kontainer.{$action}: {$status}\n";
}

echo "\n🔍 Testing Route Access:\n";

// Test routes that should be accessible (view permission)
$accessibleRoutes = [
    'pembayaran-pranota-kontainer.index' => 'view',
    'pembayaran-pranota-kontainer.show' => 'view',
    'pembayaran-pranota-kontainer.payment-form' => 'view',
];

foreach ($accessibleRoutes as $routeName => $requiredPermission) {
    $route = Route::getRoutes()->getByName($routeName);
    if ($route) {
        $middleware = $route->middleware();
        $hasPermission = $permissions[$requiredPermission];
        $hasMiddleware = in_array("permission:pembayaran-pranota-kontainer.{$requiredPermission}", $middleware);

        if ($hasMiddleware && $hasPermission) {
            echo "  ✅ {$routeName}: Accessible (has permission + middleware)\n";
        } elseif ($hasMiddleware && !$hasPermission) {
            echo "  🔒 {$routeName}: Blocked (has middleware, no permission)\n";
        } elseif (!$hasMiddleware) {
            echo "  ⚠️  {$routeName}: WARNING - No middleware protection!\n";
        }
    } else {
        echo "  ❌ {$routeName}: Route not found\n";
    }
}

// Test routes that should be blocked (no create permission)
$blockedRoutes = [
    'pembayaran-pranota-kontainer.create' => 'create',
    'pembayaran-pranota-kontainer.store' => 'create',
    'pembayaran-pranota-kontainer.edit' => 'update',
    'pembayaran-pranota-kontainer.update' => 'update',
    'pembayaran-pranota-kontainer.destroy' => 'delete',
    'pembayaran-pranota-kontainer.remove-pranota' => 'update',
];

foreach ($blockedRoutes as $routeName => $requiredPermission) {
    $route = Route::getRoutes()->getByName($routeName);
    if ($route) {
        $middleware = $route->middleware();
        $hasPermission = $permissions[$requiredPermission];
        $hasMiddleware = in_array("permission:pembayaran-pranota-kontainer.{$requiredPermission}", $middleware);

        if ($hasMiddleware && !$hasPermission) {
            echo "  🔒 {$routeName}: Correctly blocked (has middleware, no permission)\n";
        } elseif ($hasMiddleware && $hasPermission) {
            echo "  ✅ {$routeName}: Accessible (has permission + middleware)\n";
        } elseif (!$hasMiddleware) {
            echo "  🚨 {$routeName}: SECURITY ISSUE - No middleware protection!\n";
        }
    } else {
        echo "  ❌ {$routeName}: Route not found\n";
    }
}

echo "\n📊 SUMMARY:\n";
$securityIssues = 0;
foreach (Route::getRoutes() as $route) {
    $name = $route->getName();
    if (str_starts_with($name, 'pembayaran-pranota-kontainer.')) {
        $middleware = $route->middleware();
        $hasPermissionMiddleware = false;
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'permission:pembayaran-pranota-kontainer.')) {
                $hasPermissionMiddleware = true;
                break;
            }
        }
        if (!$hasPermissionMiddleware) {
            echo "🚨 SECURITY ISSUE: {$name} has no permission middleware!\n";
            $securityIssues++;
        }
    }
}

if ($securityIssues === 0) {
    echo "✅ All pembayaran-pranota-kontainer routes are properly protected!\n";
    echo "🔒 User test4 should now be blocked from accessing create payment functionality\n";
} else {
    echo "❌ Found {$securityIssues} security issues that need to be fixed\n";
}

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
