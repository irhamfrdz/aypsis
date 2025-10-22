<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Http\Middleware\EnsurePermission;
use Illuminate\Http\Request;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Middleware Manually ===\n\n";

// Simulate admin user login
$admin = User::where('username', 'admin')->first();
if ($admin) {
    echo "Admin user found: {$admin->username}\n";

    // Login the user
    auth()->login($admin);
    echo "Admin logged in: " . (auth()->check() ? 'YES' : 'NO') . "\n";
    echo "Current user: " . (auth()->user() ? auth()->user()->username : 'NONE') . "\n";

    // Test permission manually
    $hasPermission = auth()->user()->hasPermissionTo('master-pelabuhan-view');
    echo "Auth user has permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";

    // Create a mock request
    $request = Request::create('/master-pelabuhan', 'GET');

    // Test middleware
    try {
        $middleware = new EnsurePermission();
        $result = $middleware->handle($request, function($request) {
            return "Access granted!";
        }, 'master-pelabuhan-view');

        echo "Middleware result: " . $result . "\n";
    } catch (Exception $e) {
        echo "Middleware error: " . $e->getMessage() . "\n";
    }

} else {
    echo "Admin user not found!\n";
}
