<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Setting up Admin User with All Permissions ===\n\n";

// Find or create admin user
$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "Admin user not found. Creating new admin user...\n";
    $user = User::create([
        'username' => 'admin',
        'password' => Hash::make('admin123'), // Default password
        'karyawan_id' => null, // Admin doesn't need karyawan
    ]);
    echo "✅ Admin user created with username: admin, password: admin123\n";
} else {
    echo "✅ Admin user found: {$user->username} (ID: {$user->id})\n";
}

// Get all permissions in the system
$allPermissions = Permission::all();
echo "\nTotal permissions in system: {$allPermissions->count()}\n";

// Get current permissions for admin user
$currentPermissions = $user->permissions->pluck('id')->toArray();
echo "Current permissions for admin: " . count($currentPermissions) . "\n";

// Assign all permissions to admin user
$user->permissions()->sync($allPermissions->pluck('id')->toArray());

echo "\n✅ All permissions assigned to admin user!\n";

// Verify assignment
$user->refresh(); // Reload user with new permissions
$newPermissionCount = $user->permissions->count();
echo "✅ Admin now has {$newPermissionCount} permissions\n";

// List some permissions for verification
echo "\nSample permissions assigned:\n";
$samplePermissions = $user->permissions->take(10);
foreach ($samplePermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}

if ($allPermissions->count() > 10) {
    echo "  ... and " . ($allPermissions->count() - 10) . " more permissions\n";
}

echo "\n🎉 Admin user setup complete!\n";
echo "Username: admin\n";
echo "Password: admin123 (change this after first login)\n";
echo "The admin user now has access to all features in the system.\n";
