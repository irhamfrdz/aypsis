<?php

/**
 * Script khusus untuk setup Vendor Kontainer Sewa Permissions di Server
 * Jalankan dengan: php setup_vendor_kontainer_sewa_permissions_server.php
 *
 * Script ini minimal dan focused untuk server production
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "=== Vendor Kontainer Sewa Permissions Setup (Server) ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    DB::beginTransaction();

    // 1. Create permissions
    echo "1. Creating permissions...\n";
    $permissions = [
        'vendor-kontainer-sewa-view',
        'vendor-kontainer-sewa-create',
        'vendor-kontainer-sewa-edit',
        'vendor-kontainer-sewa-delete',
    ];

    $created = 0;
    foreach ($permissions as $name) {
        $permission = Permission::firstOrCreate(
            ['name' => $name],
            ['guard_name' => 'web']
        );

        if ($permission->wasRecentlyCreated) {
            $created++;
            echo "   ✓ Created: {$name}\n";
        } else {
            echo "   → Exists: {$name}\n";
        }
    }

    echo "   Summary: {$created} new permissions created\n\n";

    // 2. Find and assign to admin
    echo "2. Finding admin user...\n";

    // Try to find admin user
    $admin = User::where('username', 'admin')->first()
           ?? User::where('email', 'like', '%admin%')->first()
           ?? User::find(1);

    if (!$admin) {
        echo "   ❌ Admin user not found!\n";
        echo "   Available users:\n";
        $users = User::select('id', 'username', 'email')->limit(5)->get();
        foreach ($users as $user) {
            echo "   - ID: {$user->id}, Username: {$user->username}\n";
        }
        echo "\n   Please run manually:\n";
        echo "   php artisan tinker\n";
        echo "   >>> \$user = User::find(USER_ID);\n";
        echo "   >>> \$user->givePermissionTo(['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete']);\n\n";
        DB::rollBack();
        exit(1);
    }

    echo "   ✓ Found admin: {$admin->username} (ID: {$admin->id})\n\n";

    // 3. Assign permissions
    echo "3. Assigning permissions...\n";
    $newPerms = 0;
    foreach ($permissions as $perm) {
        if (!$admin->hasPermissionTo($perm)) {
            $admin->givePermissionTo($perm);
            $newPerms++;
            echo "   ✓ Assigned: {$perm}\n";
        } else {
            echo "   → Already has: {$perm}\n";
        }
    }

    echo "   Summary: {$newPerms} new permissions assigned\n\n";

    // 4. Verify
    echo "4. Verification...\n";
    $hasAll = true;
    foreach ($permissions as $perm) {
        $has = $admin->hasPermissionTo($perm);
        echo "   " . ($has ? "✓" : "✗") . " {$perm}\n";
        if (!$has) $hasAll = false;
    }

    if ($hasAll) {
        DB::commit();
        echo "\n✅ SUCCESS: All permissions assigned to {$admin->username}\n";
        echo "🎯 Admin can now access: /vendor-kontainer-sewa\n\n";
    } else {
        DB::rollBack();
        echo "\n❌ FAILED: Some permissions not assigned properly\n";
        exit(1);
    }

} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Please check:\n";
    echo "1. Database connection\n";
    echo "2. Spatie Permission package installed\n";
    echo "3. Migrations completed\n";
    exit(1);
}

echo "=== Setup Completed ===\n";

?>
