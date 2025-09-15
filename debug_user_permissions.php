<?php

// Test script to check user permissions and matrix conversion
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;
use ReflectionMethod;

echo "=== Checking User Permissions and Matrix Conversion ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "User: {$user->username} (ID: {$user->id})\n";
echo "User permissions from database:\n";

$userPermissions = $user->permissions->pluck('name')->toArray();
foreach ($userPermissions as $perm) {
    echo "- $perm\n";
}

// Test matrix conversion
$controller = new UserController();
$reflectionMethod = new ReflectionMethod($controller, 'convertPermissionsToMatrix');
$reflectionMethod->setAccessible(true);

$matrix = $reflectionMethod->invoke($controller, $userPermissions);

echo "\nMatrix conversion result:\n";
echo "Available modules in matrix:\n";
foreach (array_keys($matrix) as $module) {
    echo "- $module\n";
    if ($module === 'perbaikan-kontainer') {
        echo "  Actions for perbaikan-kontainer:\n";
        foreach ($matrix[$module] as $action => $value) {
            echo "    - $action: " . ($value ? 'true' : 'false') . "\n";
        }
    }
}

if (!isset($matrix['perbaikan-kontainer'])) {
    echo "\n❌ perbaikan-kontainer NOT found in matrix\n";
    echo "This means the permission checkboxes won't show in the view.\n";
} else {
    echo "\n✅ perbaikan-kontainer found in matrix - checkboxes should be visible\n";
}
