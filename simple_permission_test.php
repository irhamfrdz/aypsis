<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;
use App\Http\Middleware\EnsurePermissionWithDetails;

/**
 * Simple Test for Permission Warning System
 */

echo "🧪 Simple Permission Warning Test\n";
echo "=================================\n\n";

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get a user without master-karyawan permissions
$user = User::whereDoesntHave('permissions', function($query) {
    $query->where('name', 'like', 'master-karyawan%');
})->first();

if (!$user) {
    // If no user without permissions found, create a test user
    echo "Creating test user without master-karyawan permissions...\n";
    $user = User::create([
        'username' => 'test_no_permissions_' . time(),
        'password' => bcrypt('password'),
        'status' => 'active'
    ]);
    echo "   Created user: {$user->username}\n";
}

echo "Using user: {$user->name}\n\n";

// Check permissions
$permissions = [
    'master-karyawan',
    'master-karyawan.view',
    'master-karyawan.create'
];

echo "Current permissions:\n";
foreach ($permissions as $perm) {
    $hasPermission = $user->hasPermissionTo($perm);
    echo "  - {$perm}: " . ($hasPermission ? '✅ HAS' : '❌ MISSING') . "\n";
}

echo "\nTesting middleware directly:\n";

// Simulate authenticated user
Auth::login($user);
echo "   User authenticated: " . (Auth::check() ? '✅ YES' : '❌ NO') . "\n";

try {
    $middleware = new EnsurePermissionWithDetails();
    $request = Request::create('/test', 'GET');

    // Test middleware with a permission the user doesn't have
    $response = $middleware->handle($request, function($req) {
        return response('Should not reach here');
    }, 'master-karyawan.view');

    // Check the response
    echo "   Response status: " . $response->getStatusCode() . "\n";
    echo "   Response type: " . get_class($response) . "\n";

    if ($response->getStatusCode() == 403) {
        echo "   ✅ Middleware correctly returned 403 response\n";
        echo "   Response content length: " . strlen($response->getContent()) . " characters\n";

        // Check if response contains expected content
        $content = $response->getContent();
        $checks = [
            'Izin yang Diperlukan' => 'Required permission section',
            'master-karyawan.view' => 'Required permission mentioned',
            'Akses Ditolak' => 'Error title',
            'Izin yang Anda Miliki' => 'User permissions section',
            'Kembali ke Dashboard' => 'Navigation option'
        ];

        echo "\n   Content verification:\n";
        foreach ($checks as $search => $description) {
            $found = strpos($content, $search) !== false;
            echo "     - {$description}: " . ($found ? '✅ FOUND' : '❌ MISSING') . "\n";
        }

        // Show a preview of the content
        echo "\n   Content preview (first 500 chars):\n";
        echo "   " . substr($content, 0, 500) . "...\n";
    } else {
        echo "   ❌ Expected 403, got: " . $response->getStatusCode() . "\n";
    }

} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "✅ Middleware correctly threw AuthorizationException\n";
    echo "   Message: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "⚠️  Unexpected exception: " . get_class($e) . "\n";
    echo "   Message: " . $e->getMessage() . "\n";
}

echo "\n✅ Test completed!\n";
