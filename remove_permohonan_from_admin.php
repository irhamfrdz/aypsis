<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== REMOVING UNNECESSARY PERMOHONAN PERMISSION FROM ADMIN USER ===\n\n";

$user = User::where('username', 'admin')->first();
$permohonanPerm = Permission::where('name', 'permohonan')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

if (!$permohonanPerm) {
    echo "❌ Permission 'permohonan' not found in database!\n";
    exit(1);
}

echo "✅ Admin user found: {$user->username}\n";
echo "✅ Permission 'permohonan' found: ID {$permohonanPerm->id}\n";

// Check if user has this permission
$hasPermission = $user->permissions()->where('permission_id', $permohonanPerm->id)->exists();

if (!$hasPermission) {
    echo "ℹ️  User admin doesn't have 'permohonan' permission (already removed)\n";
} else {
    // Remove permission from user
    $user->permissions()->detach($permohonanPerm->id);
    echo "✅ Permission 'permohonan' successfully removed from admin user\n";
}

// Verify the permission was removed
$user->refresh();
$hasPermissionAfter = $user->can('permohonan');

echo "\n=== VERIFICATION ===\n";
echo "User can access permohonan: " . ($hasPermissionAfter ? 'YES ❌' : 'NO ✅') . "\n";

// Check if user has approval-dashboard permission
$hasApprovalDashboard = $user->can('approval-dashboard');
echo "User can access approval-dashboard: " . ($hasApprovalDashboard ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n=== SUMMARY ===\n";
if (!$hasPermissionAfter && $hasApprovalDashboard) {
    echo "🎉 Configuration is now CORRECT!\n";
    echo "User admin can access approval using the proper 'approval-dashboard' permission.\n";
} else {
    echo "⚠️  Configuration might still need adjustment.\n";
}

echo "\nOperation completed.\n";
