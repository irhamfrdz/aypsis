<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Adding Gate In permissions...\n";

    // Gate In permissions
    $gateInPermissions = [
        'gate-in-view' => 'Melihat data Gate In',
        'gate-in-create' => 'Membuat Gate In baru',
        'gate-in-update' => 'Mengubah data Gate In',
        'gate-in-delete' => 'Menghapus data Gate In',
    ];

    foreach ($gateInPermissions as $name => $description) {
        $permission = DB::table('permissions')->where('name', $name)->first();

        if (!$permission) {
            DB::table('permissions')->insert([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Added permission: {$name}\n";
        } else {
            echo "- Permission already exists: {$name}\n";
        }
    }

    echo "\nAdding Gate In permissions to admin role...\n";

    // Add all Gate In permissions to admin role
    $adminRole = DB::table('roles')->where('name', 'admin')->first();

    if ($adminRole) {
        foreach (array_keys($gateInPermissions) as $permissionName) {
            $permission = DB::table('permissions')->where('name', $permissionName)->first();

            if ($permission) {
                $existing = DB::table('role_has_permissions')
                    ->where('role_id', $adminRole->id)
                    ->where('permission_id', $permission->id)
                    ->first();

                if (!$existing) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $adminRole->id,
                        'permission_id' => $permission->id
                    ]);
                    echo "✓ Added {$permissionName} to admin role\n";
                } else {
                    echo "- Admin already has permission: {$permissionName}\n";
                }
            }
        }
    } else {
        echo "⚠ Admin role not found!\n";
    }

    echo "\nGate In permissions setup completed successfully!\n";
    echo "\nPermissions added:\n";
    foreach ($gateInPermissions as $name => $desc) {
        echo "- {$name}: {$desc}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
