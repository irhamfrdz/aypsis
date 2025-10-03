<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== CHECKING USER IMPORT PERMISSIONS ===\n\n";

// Get current authenticated user (assuming admin)
$adminUsers = User::where('email', 'LIKE', '%admin%')->orWhere('username', 'LIKE', '%admin%')->get();

if ($adminUsers->count() > 0) {
    foreach ($adminUsers as $user) {
        echo "👤 User: {$user->email} (ID: {$user->id})\n";

        // Check specific import-related permissions
        $importPermissions = [
            'tagihan-kontainer-sewa-create',
            'tagihan-kontainer-sewa.create',
            'tagihan-kontainer-sewa.import',
            'tagihan-kontainer-sewa-index',
            'tagihan-kontainer-sewa.index'
        ];

        foreach ($importPermissions as $permission) {
            $hasPermission = $user->hasPermissionTo($permission);
            $status = $hasPermission ? '✅' : '❌';
            echo "  $status $permission\n";
        }

        echo "\n📋 All user permissions:\n";
        $permissions = $user->getAllPermissions();
        foreach ($permissions as $perm) {
            if (strpos($perm->name, 'tagihan-kontainer') !== false) {
                echo "  ✓ {$perm->name}\n";
            }
        }
        echo "\n" . str_repeat("-", 50) . "\n";
    }
} else {
    echo "❌ No admin users found\n";

    // Show first few users
    echo "\n📋 Available users:\n";
    $users = User::take(5)->get();
    foreach ($users as $user) {
        echo "  - {$user->email} (ID: {$user->id})\n";
    }
}

echo "\n=== PERMISSION CHECK COMPLETE ===\n";
