<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔧 Fixing Missing Permissions for User test4\n";
echo "===========================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "👤 User: {$user->username} (ID: {$user->id})\n\n";

// Define the missing permissions that need to be assigned
$missingPermissions = [
    'master-karyawan.view',
    'master-kontainer.view',
    'master-tujuan.view',
    'master-kegiatan.view',
    'master-permission.view',
    'master-mobil.view'
];

echo "📋 Assigning Missing Permissions:\n";
$assignedCount = 0;

foreach ($missingPermissions as $permName) {
    $permission = Permission::where('name', $permName)->first();

    if ($permission) {
        // Check if user already has this permission
        $hasPermission = $user->permissions()->where('permission_id', $permission->id)->exists();

        if (!$hasPermission) {
            // Assign the permission
            $user->permissions()->attach($permission->id);
            echo "  ✅ Assigned: {$permName} (ID: {$permission->id})\n";
            $assignedCount++;
        } else {
            echo "  ⚠️  Already has: {$permName}\n";
        }
    } else {
        echo "  ❌ Permission not found: {$permName}\n";
    }
}

echo "\n📊 Summary:\n";
echo "  - Permissions assigned: {$assignedCount}\n";

if ($assignedCount > 0) {
    echo "\n🎉 Permissions have been assigned successfully!\n";
    echo "   The Master Data menu should now appear in the sidebar.\n";
} else {
    echo "\n⚠️  No permissions were assigned.\n";
}

// Verify the assignment
echo "\n🔍 Verification:\n";
foreach ($missingPermissions as $permName) {
    $hasPermission = $user->hasPermissionTo($permName);
    $status = $hasPermission ? '✅ HAS' : '❌ MISSING';
    echo "  - {$permName}: {$status}\n";
}

$masterKaryawanView = $user->can('master-karyawan.view');
echo "\n📊 Sidebar Access: " . ($masterKaryawanView ? '✅ ACCESSIBLE' : '❌ NOT ACCESSIBLE') . "\n";
