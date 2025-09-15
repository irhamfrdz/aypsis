<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Fixing permission inconsistency for user test4\n";
echo "================================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Check current permissions
$permissions = $user->permissions;
echo "Current permissions:\n";
foreach ($permissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// Remove the old generic permission
if ($user->hasPermissionTo('master-karyawan')) {
    // Find the permission and detach it
    $permission = Permission::where('name', 'master-karyawan')->first();
    if ($permission) {
        $user->permissions()->detach($permission->id);
        echo "✅ Removed old permission: master-karyawan\n";
    }
}

// Since the user should not have access to karyawan data according to the form,
// we don't give any master-karyawan.* permissions

echo "\n📋 Summary of changes:\n";
echo "  - Removed: master-karyawan\n";
echo "  - Added: None (user should not have karyawan access)\n\n";

// Verify the changes
echo "Verifying permissions after changes:\n";
$hasOldPermission = $user->hasPermissionTo('master-karyawan');
$hasViewPermission = $user->hasPermissionTo('master-karyawan.view');

echo "  - master-karyawan: " . ($hasOldPermission ? '❌ STILL HAS' : '✅ REMOVED') . "\n";
echo "  - master-karyawan.view: " . ($hasViewPermission ? '❌ HAS' : '✅ DOES NOT HAVE') . "\n\n";

echo "🎉 Permission fix completed!\n";
echo "User test4 should now be blocked from accessing karyawan data.\n";
