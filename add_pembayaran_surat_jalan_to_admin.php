<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

try {
    echo "Adding Pembayaran Surat Jalan permissions to admin users...\n";
    
    // Cari permissions pembayaran surat jalan
    $permissions = Permission::where('name', 'LIKE', 'pembayaran-surat-jalan-%')->get();
    
    if ($permissions->isEmpty()) {
        echo "❌ No pembayaran surat jalan permissions found. Run the add permissions script first.\n";
        exit(1);
    }

    echo "Found " . $permissions->count() . " pembayaran surat jalan permissions:\n";
    foreach ($permissions as $permission) {
        echo "- {$permission->name}\n";
    }
    echo "\n";

    // Cari user admin (asumsi username admin)
    $adminUsers = User::whereIn('username', ['admin', 'administrator', 'superadmin'])->get();
    
    if ($adminUsers->isEmpty()) {
        echo "❌ No admin users found. Please specify admin username manually.\n";
        
        // Show available users
        $users = User::select('id', 'username')->limit(10)->get();
        echo "\nAvailable users (first 10):\n";
        foreach ($users as $user) {
            echo "- ID: {$user->id}, Username: {$user->username}\n";
        }
        exit(1);
    }

    foreach ($adminUsers as $admin) {
        echo "Adding permissions to user: {$admin->username} (ID: {$admin->id})\n";
        
        // Get current permission IDs
        $currentPermissionIds = $admin->permissions()->pluck('permissions.id')->toArray();
        
        // Get new permission IDs
        $newPermissionIds = $permissions->pluck('id')->toArray();
        
        // Merge with existing permissions (without duplicates)
        $allPermissionIds = array_unique(array_merge($currentPermissionIds, $newPermissionIds));
        
        // Sync permissions
        $admin->permissions()->sync($allPermissionIds);
        
        echo "✅ Added " . count($newPermissionIds) . " permissions to {$admin->username}\n";
    }

    echo "\n🎉 Successfully added Pembayaran Surat Jalan permissions to admin users!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}