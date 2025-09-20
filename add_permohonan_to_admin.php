<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== ADDING PERMOHONAN PERMISSION TO ADMIN USER ===\n\n";

$user = User::where('username', 'admin')->first();
$permohonanPerm = Permission::where('name', 'permohonan')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

if (!$permohonanPerm) {
    echo "❌ Permission 'permohonan' not found in database!\n";
    echo "Available permohonan-related permissions:\n";
    $permohonanRelated = Permission::where('name', 'like', '%permohonan%')->get();
    foreach ($permohonanRelated as $perm) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
    exit(1);
}

echo "✅ Admin user found: {$user->username}\n";
echo "✅ Permission 'permohonan' found: ID {$permohonanPerm->id}\n";

// Check if user already has this permission
$hasPermission = $user->permissions()->where('permission_id', $permohonanPerm->id)->exists();

if ($hasPermission) {
    echo "ℹ️  User admin already has 'permohonan' permission\n";
} else {
    // Add permission to user
    $user->permissions()->attach($permohonanPerm->id);
    echo "✅ Permission 'permohonan' successfully added to admin user\n";
}

// Verify the permission was added
$user->refresh();
$hasPermissionAfter = $user->can('permohonan');

echo "\n=== VERIFICATION ===\n";
echo "User can access permohonan: " . ($hasPermissionAfter ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n=== ALL USER PERMISSIONS (AFTER UPDATE) ===\n";
$permissions = $user->permissions()->orderBy('name')->get();
foreach ($permissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\nOperation completed.\n";
