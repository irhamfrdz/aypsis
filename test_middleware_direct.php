<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Middleware\EnsurePermissionWithDetails;

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found\n";
    exit(1);
}

// Simulate authentication
Auth::login($user);
echo "Testing with user: {$user->name}\n";
echo "User permissions: " . json_encode($user->permissions->pluck('name')->toArray()) . "\n\n";

// Test middleware directly
echo "Testing middleware directly...\n";

try {
    $middleware = new EnsurePermissionWithDetails();
    $request = Request::create('/test', 'GET');

    // Test with a permission the user doesn't have
    $response = $middleware->handle($request, function($req) {
        return response('Success - should not reach here');
    }, 'master-karyawan.view');

    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response type: " . get_class($response) . "\n";

    if ($response->getStatusCode() == 403) {
        echo "✅ Middleware correctly returned 403 response\n";
        $content = $response->getContent();
        if (strpos($content, 'master-karyawan.view') !== false) {
            echo "✅ Response contains required permission\n";
        }
        if (strpos($content, 'Izin yang Diperlukan') !== false) {
            echo "✅ Response contains Indonesian text (custom error page)\n";
        }
    } else {
        echo "❌ Expected 403, got: " . $response->getStatusCode() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Test completed!\n";
