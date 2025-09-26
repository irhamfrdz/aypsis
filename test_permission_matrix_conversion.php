<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Permission;

// Simulate permission matrix data like it comes from the form
$permissionsMatrix = [
    'pembayaran-pranota-cat' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

echo "=== TESTING convertMatrixPermissionsToIds ===\n\n";

// Create controller instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Test the method
try {
    $result = $method->invoke($controller, $permissionsMatrix);
    echo "✅ Method executed successfully\n";
    echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

    // Check if permissions were found
    if (!empty($result)) {
        echo "✅ Found " . count($result) . " permission IDs\n";
        foreach ($result as $id) {
            $permission = Permission::find($id);
            if ($permission) {
                echo "  - ID {$id}: {$permission->name}\n";
            } else {
                echo "  - ID {$id}: NOT FOUND\n";
            }
        }
    } else {
        echo "❌ No permission IDs found\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Check Laravel logs for debug output
echo "\n=== CHECKING LARAVEL LOGS ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $debugLines = array_filter($lines, function($line) {
        return strpos($line, 'DEBUG: Processing pembayaran-pranota-cat') !== false;
    });

    if (!empty($debugLines)) {
        echo "✅ Found debug logs:\n";
        foreach ($debugLines as $line) {
            echo "  " . trim($line) . "\n";
        }
    } else {
        echo "❌ No debug logs found for pembayaran-pranota-cat\n";
    }
} else {
    echo "❌ Log file not found\n";
}

echo "\n=== CHECKING PERMISSIONS IN DATABASE ===\n";
$catPermissions = Permission::where('name', 'like', 'pembayaran-pranota-cat%')->get();
if ($catPermissions->count() > 0) {
    echo "✅ Found " . $catPermissions->count() . " pembayaran-pranota-cat permissions:\n";
    foreach ($catPermissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "❌ No pembayaran-pranota-cat permissions found in database\n";
}
