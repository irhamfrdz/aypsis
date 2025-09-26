<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;

echo "=== TESTING FULL UPDATE PROCESS ===\n\n";

// Get a test user
$user = User::find(1); // Assuming user with ID 1 exists
if (!$user) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "✅ Found test user: {$user->name} (ID: {$user->id})\n\n";

// Create controller instance
$controller = new UserController();

// Simulate form data with pembayaran-pranota-cat permissions
$formData = [
    'name' => $user->name,
    'username' => $user->username,
    'karyawan_id' => $user->karyawan_id,
    'permissions' => [
        'pembayaran-pranota-cat' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ]
    ]
];

// Create a mock request
$request = new Request();
$request->merge($formData);

// Use reflection to access private update method
$reflection = new ReflectionClass($controller);
$updateMethod = $reflection->getMethod('update');
$updateMethod->setAccessible(true);

// Test the update method
try {
    echo "🔄 Calling update method...\n";
    $response = $updateMethod->invoke($controller, $request, $user);
    echo "✅ Update method executed successfully\n\n";

    // Check if user now has the permissions
    $userPermissions = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();
    echo "🔍 Checking user permissions after update:\n";
    if ($userPermissions->count() > 0) {
        echo "✅ User has " . $userPermissions->count() . " pembayaran-pranota-cat permissions:\n";
        foreach ($userPermissions as $perm) {
            echo "  - {$perm->name}\n";
        }
    } else {
        echo "❌ User has no pembayaran-pranota-cat permissions\n";
    }

} catch (Exception $e) {
    echo "❌ Error in update method: " . $e->getMessage() . "\n";
}

// Check Laravel logs for debug output
echo "\n=== CHECKING LARAVEL LOGS FOR DEBUG OUTPUT ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $lines = explode("\n", $content);
    $debugLines = array_filter($lines, function($line) {
        return strpos($line, 'DEBUG:') !== false;
    });

    $recentDebugLines = array_slice($debugLines, -10); // Get last 10 debug lines

    if (!empty($recentDebugLines)) {
        echo "✅ Found recent debug logs:\n";
        foreach ($recentDebugLines as $line) {
            if (trim($line)) {
                echo "  " . trim($line) . "\n";
            }
        }
    } else {
        echo "❌ No debug logs found\n";
    }
} else {
    echo "❌ Log file not found\n";
}
