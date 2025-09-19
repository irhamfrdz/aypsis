<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

// Get admin user and authenticate
$user = User::with('permissions')->where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found!\n";
    exit;
}

Auth::login($user);

echo "=== END-TO-END TEST ===\n";
echo "Authenticated as: {$user->username}\n";

// Test route access
echo "\n=== TESTING ROUTE ACCESS ===\n";

try {
    // Test if route exists
    $route = Route::getRoutes()->getByName('master.kode-nomor.index');
    if ($route) {
        echo "✅ Route 'master.kode-nomor.index' exists\n";

        // Test middleware on the route
        $middleware = $route->middleware();
        $hasCanMiddleware = false;
        foreach ($middleware as $m) {
            if (strpos($m, 'can:') === 0) {
                echo "✅ Route has middleware: $m\n";
                $hasCanMiddleware = true;
                break;
            }
        }

        if (!$hasCanMiddleware) {
            echo "❌ Route does not have 'can' middleware\n";
        }

    } else {
        echo "❌ Route 'master.kode-nomor.index' not found\n";
    }

} catch (\Exception $e) {
    echo "❌ Route test error: " . $e->getMessage() . "\n";
}

// Test view rendering simulation
echo "\n=== TESTING SIDEBAR LOGIC SIMULATION ===\n";

$userCanKodeNomor = $user->can('master-kode-nomor-view');
echo "User can 'master-kode-nomor-view': " . ($userCanKodeNomor ? 'TRUE' : 'FALSE') . "\n";

if ($user && $userCanKodeNomor) {
    echo "✅ Sidebar condition would PASS - menu should be visible\n";
} else {
    echo "❌ Sidebar condition would FAIL - menu would be hidden\n";
}

echo "\n=== FINAL CONCLUSION ===\n";
if ($userCanKodeNomor) {
    echo "🎉 Kode nomor menu SHOULD be visible in sidebar now!\n";
    echo "If it's still not showing, check:\n";
    echo "1. Browser cache (hard refresh)\n";
    echo "2. View cache cleared (already done)\n";
    echo "3. User is actually logged in as admin\n";
    echo "4. No JavaScript errors hiding the menu\n";
} else {
    echo "❌ There's still a permission issue\n";
}
