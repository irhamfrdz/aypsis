<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST PERMOHONAN ROUTE ACCESS FOR USER TEST2 ===\n\n";

// Find user test2
$user = User::where('name', 'test2')->first();

if (!$user) {
    echo "❌ User 'test2' tidak ditemukan\n";
    exit(1);
}

echo "✅ User ditemukan: {$user->name} (ID: {$user->id})\n\n";

// Simulate authentication
Auth::login($user);

echo "=== TESTING PERMOHONAN ROUTES ===\n";

// Test routes that should now work
$routesToTest = [
    'permohonan.index',
    'permohonan.create',
    'permohonan.export',
    'permohonan.import',
    'permohonan.print',
    'permohonan.bulk-delete'
];

foreach ($routesToTest as $routeName) {
    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            // Check if user can access this route
            $middleware = $route->middleware();
            $canAccess = true;

            foreach ($middleware as $mw) {
                if (strpos($mw, 'can:') === 0) {
                    $permission = str_replace('can:', '', $mw);
                    if (!$user->can($permission)) {
                        $canAccess = false;
                        break;
                    }
                }
            }

            $status = $canAccess ? '✅ ACCESSIBLE' : '❌ UNAUTHORIZED';
            echo "Route '{$routeName}': {$status}\n";
        } else {
            echo "Route '{$routeName}': ❌ NOT FOUND\n";
        }
    } catch (Exception $e) {
        echo "Route '{$routeName}': ❌ ERROR - {$e->getMessage()}\n";
    }
}

echo "\n=== PERMISSION VERIFICATION ===\n";
echo "User has permission 'permohonan': " . ($user->can('permohonan') ? '✅ YES' : '❌ NO') . "\n";
echo "User has permission 'master-permohonan': " . ($user->can('master-permohonan') ? '✅ YES' : '❌ NO') . "\n";

echo "\n🎯 CONCLUSION:\n";
if ($user->can('permohonan')) {
    echo "✅ User test2 should now be able to access all permohonan routes!\n";
} else {
    echo "❌ User test2 still doesn't have the required permission\n";
}
