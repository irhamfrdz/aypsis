<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

// Get admin user (assuming user ID 1 is admin)
$admin = User::find(1);

if ($admin) {
    echo "Admin user: " . $admin->name . "\n";

    // Get all permissions
    $allPermissions = Permission::all();
    $grantedCount = 0;

    foreach ($allPermissions as $permission) {
        // Check if admin already has this permission
        $existing = DB::table('user_permissions')
            ->where('user_id', $admin->id)
            ->where('permission_id', $permission->id)
            ->first();

        if (!$existing) {
            DB::table('user_permissions')->insert([
                'user_id' => $admin->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Granted: {$permission->name}\n";
            $grantedCount++;
        } else {
            echo "⚠️  Already has: {$permission->name}\n";
        }
    }

    echo "\n📊 Summary: Granted {$grantedCount} new permissions\n";

    // Check all admin permissions
    echo "\nAdmin permissions:\n";
    foreach ($admin->permissions as $permission) {
        echo "- " . $permission->name . "\n";
    }
} else {
    echo "Admin user not found!\n";
}

echo "\nCompleted!\n";
