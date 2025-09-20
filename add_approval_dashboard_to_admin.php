<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== ADDING CORRECT APPROVAL-DASHBOARD PERMISSION TO ADMIN USER ===\n\n";

$user = User::where('username', 'admin')->first();
$approvalDashboardPerm = Permission::where('name', 'approval-dashboard')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

if (!$approvalDashboardPerm) {
    echo "❌ Permission 'approval-dashboard' not found in database!\n";
    echo "Available approval permissions:\n";
    $approvalPerms = Permission::where('name', 'like', 'approval-%')->get();
    foreach ($approvalPerms as $perm) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
    exit(1);
}

echo "✅ Admin user found: {$user->username}\n";
echo "✅ Permission 'approval-dashboard' found: ID {$approvalDashboardPerm->id}\n";

// Check if user already has this permission
$hasPermission = $user->permissions()->where('permission_id', $approvalDashboardPerm->id)->exists();

if ($hasPermission) {
    echo "ℹ️  User admin already has 'approval-dashboard' permission\n";
} else {
    // Add permission to user
    $user->permissions()->attach($approvalDashboardPerm->id);
    echo "✅ Permission 'approval-dashboard' successfully added to admin user\n";
}

// Verify the permission was added
$user->refresh();
$hasPermissionAfter = $user->can('approval-dashboard');

echo "\n=== VERIFICATION ===\n";
echo "User can access approval-dashboard: " . ($hasPermissionAfter ? 'YES ✅' : 'NO ❌') . "\n";
echo "User can access permohonan: " . ($user->can('permohonan') ? 'YES ❌' : 'NO ✅') . "\n";

echo "\n=== ALL APPROVAL PERMISSIONS FOR ADMIN ===\n";
$approvalPerms = $user->permissions()->where('name', 'like', 'approval-%')->get();
if ($approvalPerms->count() > 0) {
    foreach ($approvalPerms as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "No approval permissions found!\n";
}

echo "\n=== SUMMARY ===\n";
if ($hasPermissionAfter && !$user->can('permohonan')) {
    echo "🎉 Configuration is now PERFECT!\n";
    echo "User admin can access approval using the correct 'approval-dashboard' permission.\n";
    echo "The unnecessary 'permohonan' permission has been removed.\n";
} else {
    echo "⚠️  Configuration still needs adjustment.\n";
}

echo "\nOperation completed.\n";
