<?php

/**
 * Grant All Permissions to Admin User
 * Usage: php grant_admin_all_permissions.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "==========================================\n";
echo "   Grant All Permissions to Admin User\n";
echo "==========================================\n";

try {
    // Find admin user
    $adminUser = User::where('username', 'admin')->first();

    if (!$adminUser) {
        throw new Exception('Admin user not found. Please create admin user first.');
    }

    echo "✅ Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n";

    // Get all permissions from database
    $allPermissions = Permission::all();

    if ($allPermissions->isEmpty()) {
        throw new Exception('No permissions found in database. Please run seeders first.');
    }

    echo "📊 Found {$allPermissions->count()} permissions in database\n";

    // Get permission IDs
    $permissionIds = $allPermissions->pluck('id')->toArray();

    // Get current admin permissions
    $currentPermissions = $adminUser->permissions->pluck('name')->toArray();
    echo "📋 Current admin permissions: " . count($currentPermissions) . "\n";

    // Assign all permissions to admin user
    $adminUser->permissions()->sync($permissionIds);

    // Refresh user to get updated permissions
    $adminUser->refresh();
    $newPermissions = $adminUser->permissions->pluck('name')->toArray();

    echo "✅ Successfully assigned all permissions to admin user\n";
    echo "📊 New permission count: " . count($newPermissions) . "\n\n";

    // Show summary by module
    echo "📋 Permission Summary by Module:\n";
    echo "================================\n";

    $moduleGroups = [
        'master' => ['master-karyawan', 'master-user', 'master-kontainer', 'master-tujuan', 'master-kegiatan', 'master-permission', 'master-mobil', 'master-divisi', 'master-pekerjaan'],
        'tagihan' => ['tagihan-kontainer', 'pranota', 'pembayaran-pranota-kontainer'],
        'perbaikan' => ['perbaikan-kontainer', 'pranota-perbaikan-kontainer', 'pembayaran-pranota-perbaikan-kontainer'],
        'supir' => ['permohonan', 'pranota-supir', 'pembayaran-pranota-supir'],
        'approval' => ['approval', 'user-approval'],
        'dashboard' => ['dashboard'],
        'admin' => ['admin'],
        'profile' => ['profile'],
        'auth' => ['login', 'logout']
    ];

    foreach ($moduleGroups as $groupName => $modules) {
        $groupPermissions = array_filter($newPermissions, function($perm) use ($modules) {
            foreach ($modules as $module) {
                if (strpos($perm, $module) === 0) {
                    return true;
                }
            }
            return false;
        });

        if (!empty($groupPermissions)) {
            echo strtoupper($groupName) . " MODULES (" . count($groupPermissions) . "):\n";
            foreach ($groupPermissions as $perm) {
                echo "  - {$perm}\n";
            }
            echo "\n";
        }
    }

    // Show other permissions not in main groups
    $otherPermissions = array_filter($newPermissions, function($perm) use ($moduleGroups) {
        foreach ($moduleGroups as $modules) {
            foreach ($modules as $module) {
                if (strpos($perm, $module) === 0) {
                    return false;
                }
            }
        }
        return true;
    });

    if (!empty($otherPermissions)) {
        echo "OTHER PERMISSIONS (" . count($otherPermissions) . "):\n";
        foreach ($otherPermissions as $perm) {
            echo "  - {$perm}\n";
        }
        echo "\n";
    }

    echo "==========================================\n";
    echo "   Admin User Permission Grant Complete!\n";
    echo "==========================================\n";
    echo "✅ Admin user now has access to ALL features\n";
    echo "✅ Total permissions: " . count($newPermissions) . "\n\n";

    echo "🔍 Verification Commands:\n";
    echo "php artisan tinker\n";
    echo "\$user = App\\Models\\User::where('username', 'admin')->first();\n";
    echo "echo \$user->permissions->count();\n\n";

    echo "🌐 Web Interface:\n";
    echo "- Login as admin\n";
    echo "- All menu items should be accessible\n";
    echo "- All features should work without permission errors\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check the error and try again.\n";
    exit(1);
}

echo "==========================================\n";
