<?php

// test_convert_permissions_to_matrix.php
// Script untuk test method convertPermissionsToMatrix dengan tagihan-cat

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING convertPermissionsToMatrix FOR TAGIHAN-CAT ===\n";
echo "=========================================================\n\n";

// Test data - user admin
$user = User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ User admin not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Get user's current tagihan-cat permissions
$userTagihanCatPermissions = $user->permissions()
    ->where('name', 'like', 'tagihan-cat%')
    ->pluck('name')
    ->toArray();

echo "User's current tagihan-cat permissions:\n";
print_r($userTagihanCatPermissions);
echo "\n";

// Test convertPermissionsToMatrix method
echo "=== TESTING convertPermissionsToMatrix ===\n";
try {
    $controller = new UserController();

    // Access private method using reflection
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertPermissionsToMatrix');
    $method->setAccessible(true);

    $matrixPermissions = $method->invoke($controller, $userTagihanCatPermissions);

    echo "Converted matrix permissions:\n";
    print_r($matrixPermissions);

    // Check if tagihan-cat is in the matrix
    if (isset($matrixPermissions['tagihan-cat'])) {
        echo "\n✅ SUCCESS: tagihan-cat found in matrix permissions\n";
        echo "Tagihan-cat matrix data:\n";
        print_r($matrixPermissions['tagihan-cat']);

        // Count enabled permissions
        $enabledCount = 0;
        foreach ($matrixPermissions['tagihan-cat'] as $action => $enabled) {
            if ($enabled === true || $enabled == '1') {
                $enabledCount++;
                echo "✓ {$action} permission enabled\n";
            }
        }
        echo "\nTotal enabled permissions: {$enabledCount}\n";
    } else {
        echo "\n❌ ERROR: tagihan-cat not found in matrix permissions\n";
        echo "Available modules in matrix:\n";
        print_r(array_keys($matrixPermissions));
    }

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== CONCLUSION ===\n";
echo "If tagihan-cat appears in matrix permissions with correct actions,\n";
echo "then the convertPermissionsToMatrix method is working correctly.\n";
