<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== FIXING USER ADMIN ROLE ASSIGNMENT ===\n";

$user = User::where('username', 'user_admin')->first();

if (!$user) {
    echo "❌ User 'user_admin' not found!\n";
    exit(1);
}

echo "✅ User found: {$user->username}\n";

// Check if admin role exists
$adminRole = Role::where('name', 'admin')->first();

if (!$adminRole) {
    echo "❌ Admin role not found! Creating admin role...\n";

    // Create admin role if it doesn't exist
    $adminRole = Role::create([
        'name' => 'admin',
        'description' => 'Administrator Sistem - Akses Penuh'
    ]);

    echo "✅ Admin role created!\n";
}

// Check if user already has admin role
$hasAdminRole = $user->roles()->where('name', 'admin')->exists();

if ($hasAdminRole) {
    echo "✅ User already has admin role!\n";
} else {
    echo "🔧 Assigning admin role to user...\n";

    // Assign admin role to user
    $user->roles()->attach($adminRole->id);

    echo "✅ Admin role assigned successfully!\n";
}

// Verify the assignment
$user->refresh(); // Refresh user data
$hasAdminRoleAfter = $user->hasRole('admin');

echo "\n🔍 Verification:\n";
echo "hasRole('admin'): " . ($hasAdminRoleAfter ? '✅ True' : '❌ False') . "\n";
echo "Roles count: " . $user->roles()->count() . "\n";

echo "\n🎉 Fix completed! Please clear cache and try again.\n";
echo "Run these commands:\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan view:clear\n";
echo "php artisan route:clear\n";