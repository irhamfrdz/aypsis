<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== TESTING APPROVAL MATRIX PERMISSIONS ===\n\n";

// Get admin user
$user = User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ Admin user not found\n";
    exit(1);
}

echo "✅ Admin user found: {$user->username}\n\n";

// Test convertPermissionsToMatrix
$userController = new App\Http\Controllers\UserController();
$userPermissions = $user->permissions->pluck('name')->toArray();
$matrixPermissions = $userController->convertPermissionsToMatrix($userPermissions);

echo "=== CURRENT USER PERMISSIONS ===\n";
foreach ($userPermissions as $perm) {
    echo "- $perm\n";
}

echo "\n=== MATRIX PERMISSIONS FOR APPROVAL ===\n";
if (isset($matrixPermissions['approval'])) {
    foreach ($matrixPermissions['approval'] as $action => $value) {
        echo "- approval.$action: " . ($value ? 'YES ✅' : 'NO ❌') . "\n";
    }
} else {
    echo "❌ No approval permissions in matrix\n";
}

echo "\n=== CHECKING SPECIFIC APPROVAL PERMISSIONS ===\n";
$approvalPermissions = ['approval-dashboard', 'approval.view', 'approval.approve', 'approval.print'];
foreach ($approvalPermissions as $perm) {
    $hasPermission = $user->permissions()->where('name', $perm)->exists();
    $canAccess = $user->can($perm);
    echo "- $perm: " . ($hasPermission ? 'HAS' : 'NO') . " | can('$perm'): " . ($canAccess ? 'YES ✅' : 'NO ❌') . "\n";
}

echo "\nTest completed.\n";
