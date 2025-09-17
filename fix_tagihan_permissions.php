<?php

/**
 * Fix Tagihan Kontainer Permission Inconsistencies
 * Usage: php fix_tagihan_permissions.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "==========================================\n";
echo "   Fix Tagihan Kontainer Permissions\n";
echo "==========================================\n";

try {
    // Find admin user
    $adminUser = User::where('username', 'admin')->first();

    if (!$adminUser) {
        throw new Exception('Admin user not found');
    }

    echo "✅ Found admin user: {$adminUser->username}\n";

    // Check which permissions are missing
    $missingPerms = [
        'tagihan-kontainer.view',
        'tagihan-kontainer.create',
        'tagihan-kontainer.update',
        'tagihan-kontainer.delete'
    ];

    $existingPerms = [
        'tagihan-kontainer-view',
        'tagihan-kontainer-create',
        'tagihan-kontainer-update',
        'tagihan-kontainer-delete'
    ];

    echo "🔍 Checking permission inconsistencies:\n";

    foreach ($missingPerms as $index => $missingPerm) {
        $existingPerm = $existingPerms[$index];

        $missingExists = Permission::where('name', $missingPerm)->first();
        $existingExists = Permission::where('name', $existingPerm)->first();

        if (!$missingExists && $existingExists) {
            echo "⚠️  Missing: {$missingPerm} | Exists: {$existingPerm}\n";
            echo "   → Creating missing permission: {$missingPerm}\n";

            // Create the missing permission with same guard_name
            $newPerm = Permission::create([
                'name' => $missingPerm,
                'guard_name' => $existingExists->guard_name
            ]);

            echo "   ✅ Created permission: {$missingPerm} (ID: {$newPerm->id})\n";

            // Assign to admin user
            $adminUser->permissions()->attach($newPerm->id);
            echo "   ✅ Assigned to admin user\n";
        } elseif ($missingExists) {
            echo "✅ {$missingPerm} already exists\n";
        } else {
            echo "❌ Neither {$missingPerm} nor {$existingPerm} exist - creating both\n";

            // Create both permissions
            $newPerm1 = Permission::create([
                'name' => $missingPerm,
                'guard_name' => 'web'
            ]);

            $newPerm2 = Permission::create([
                'name' => $existingPerm,
                'guard_name' => 'web'
            ]);

            echo "   ✅ Created: {$missingPerm} (ID: {$newPerm1->id})\n";
            echo "   ✅ Created: {$existingPerm} (ID: {$newPerm2->id})\n";

            // Assign both to admin
            $adminUser->permissions()->attach([$newPerm1->id, $newPerm2->id]);
            echo "   ✅ Assigned both to admin user\n";
        }
    }

    // Verify all permissions are now assigned
    echo "\n🔍 Final verification:\n";

    $adminUser->refresh();
    $adminPerms = $adminUser->permissions->pluck('name')->toArray();

    foreach ($missingPerms as $perm) {
        if (in_array($perm, $adminPerms)) {
            echo "✅ Admin has: {$perm}\n";
        } else {
            echo "❌ Admin missing: {$perm}\n";
        }
    }

    // Test permission checks
    echo "\n🧪 Testing permission checks:\n";

    foreach ($missingPerms as $perm) {
        $canAccess = $adminUser->can($perm);
        $status = $canAccess ? "✅" : "❌";
        echo "{$status} Can access {$perm}: {$canAccess}\n";
    }

    $totalPerms = $adminUser->permissions->count();
    echo "\n📊 Admin now has {$totalPerms} permissions\n";

    echo "\n==========================================\n";
    echo "   Fix Complete!\n";
    echo "==========================================\n";
    echo "🎉 Tagihan kontainer permissions are now consistent!\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
