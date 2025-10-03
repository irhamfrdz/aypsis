<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== CHECKING ADMIN USER PERMISSIONS FOR IMPORT ===\n\n";

// Get admin user
$admin = User::where('username', 'admin')->first();

if ($admin) {
    echo "👤 User: {$admin->username} (ID: {$admin->id})\n";
    echo "📋 Status: {$admin->status}\n\n";

    // Check specific import-related permissions
    $importPermissions = [
        'tagihan-kontainer-sewa-create',
        'tagihan-kontainer-sewa.create',
        'tagihan-kontainer-sewa.import',
        'tagihan-kontainer-sewa-index',
        'tagihan-kontainer-sewa.index'
    ];

    echo "🔍 Checking import-related permissions:\n";
    foreach ($importPermissions as $permission) {
        try {
            $hasPermission = $admin->hasPermissionTo($permission);
            $status = $hasPermission ? '✅' : '❌';
            echo "  $status $permission\n";
        } catch (Exception $e) {
            echo "  ❓ $permission (Permission doesn't exist)\n";
        }
    }

    echo "\n📋 All tagihan-kontainer permissions for this user:\n";
    $permissions = $admin->getAllPermissions();
    $found = false;
    foreach ($permissions as $perm) {
        if (strpos($perm->name, 'tagihan-kontainer') !== false) {
            echo "  ✓ {$perm->name}\n";
            $found = true;
        }
    }

    if (!$found) {
        echo "  ❌ No tagihan-kontainer permissions found\n";

        echo "\n📋 All user permissions:\n";
        foreach ($permissions as $perm) {
            echo "  ✓ {$perm->name}\n";
        }
    }

} else {
    echo "❌ Admin user not found\n";

    // Show available users
    echo "\n📋 Available users:\n";
    $users = User::take(5)->get();
    foreach ($users as $user) {
        echo "  - {$user->username} (ID: {$user->id}) - Status: {$user->status}\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";

// Also check what permissions exist in the system
echo "\n🔍 Available permissions in system:\n";
$allPermissions = Permission::where('name', 'LIKE', '%tagihan-kontainer%')
                            ->orWhere('name', 'LIKE', '%import%')
                            ->orderBy('name')
                            ->get();

if ($allPermissions->count() > 0) {
    foreach ($allPermissions as $perm) {
        echo "  📜 {$perm->name}\n";
    }
} else {
    echo "  ❌ No relevant permissions found in database\n";
}

echo "\n=== PERMISSION CHECK COMPLETE ===\n";
