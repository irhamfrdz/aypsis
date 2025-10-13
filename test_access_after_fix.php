<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== Testing Master Tujuan Kegiatan Utama Access After Route Fix ===\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n";

// Simulate authentication
Illuminate\Support\Facades\Auth::login($user);

echo "\n=== Checking Permissions ===\n";
$permissionsToCheck = [
    'master-tujuan-kegiatan-utama-view',
    'master-tujuan-kegiatan-utama-create',
    'master-tujuan-kegiatan-utama-update',
    'master-tujuan-kegiatan-utama-delete'
];

foreach ($permissionsToCheck as $perm) {
    $hasPermission = $user->can($perm);
    echo ($hasPermission ? "✅" : "❌") . " {$perm}: " . ($hasPermission ? "YES" : "NO") . "\n";
}

echo "\n=== Testing Route Access ===\n";
$routesToTest = [
    'master.tujuan-kegiatan-utama.index',
    'master.tujuan-kegiatan-utama.create',
    'master.tujuan-kegiatan-utama.store',
    'master.tujuan-kegiatan-utama.show',
    'master.tujuan-kegiatan-utama.edit',
    'master.tujuan-kegiatan-utama.update',
    'master.tujuan-kegiatan-utama.destroy'
];

foreach ($routesToTest as $routeName) {
    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            echo "✅ Route {$routeName}: EXISTS\n";

            // Test middleware
            $middlewares = $route->middleware();
            echo "   Middlewares: " . implode(', ', $middlewares) . "\n";

        } else {
            echo "❌ Route {$routeName}: NOT FOUND\n";
        }
    } catch (Exception $e) {
        echo "❌ Route {$routeName}: ERROR - {$e->getMessage()}\n";
    }
}

echo "\n=== Summary ===\n";
$viewPermission = $user->can('master-tujuan-kegiatan-utama-view');
if ($viewPermission) {
    echo "🎉 Admin user should now have access to Master Tujuan Kegiatan Utama!\n";
    echo "📍 Try accessing: http://localhost/master/tujuan-kegiatan-utama\n";
} else {
    echo "❌ Admin user still cannot access the menu. Check permissions.\n";
}