<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking and fixing pranota-perbaikan-kontainer permissions for user marlina\n";
echo "==========================================================================\n\n";

// Find user marlina
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "❌ User marlina not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Check current permissions
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions:\n";
foreach ($currentPermissions as $perm) {
    echo "  - $perm\n";
}
echo "\n";

// Check if user has the correct permission
$correctPermission = 'pranota-perbaikan-kontainer-view';
$wrongPermission = 'pranota-perbaikan-kontainer.view';

$hasCorrect = in_array($correctPermission, $currentPermissions);
$hasWrong = in_array($wrongPermission, $currentPermissions);

echo "Permission check:\n";
echo "  - $correctPermission: " . ($hasCorrect ? "✅ HAS" : "❌ MISSING") . "\n";
echo "  - $wrongPermission: " . ($hasWrong ? "✅ HAS" : "❌ MISSING") . "\n\n";

if (!$hasCorrect) {
    // Find the correct permission
    $permission = Permission::where('name', $correctPermission)->first();
    if ($permission) {
        // Add the correct permission
        $user->permissions()->attach($permission->id);
        echo "✅ Added permission: $correctPermission\n";
    } else {
        echo "❌ Permission $correctPermission not found in database\n";
    }
} else {
    echo "✅ User already has correct permission\n";
}

if ($hasWrong) {
    // Remove the wrong permission
    $wrongPerm = Permission::where('name', $wrongPermission)->first();
    if ($wrongPerm) {
        $user->permissions()->detach($wrongPerm->id);
        echo "✅ Removed wrong permission: $wrongPermission\n";
    }
}

// Verify final permissions
$user->refresh();
$finalPermissions = $user->permissions->pluck('name')->toArray();
echo "\nFinal permissions for pranota-perbaikan-kontainer:\n";
foreach ($finalPermissions as $perm) {
    if (strpos($perm, 'pranota-perbaikan-kontainer') !== false) {
        echo "  - $perm\n";
    }
}

echo "\n✅ Permission fix completed\n";
