<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Adding Pranota Rit Permissions to Admin User ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

// Get all pranota-rit and pranota-rit-kenek permissions
$pranotaRitPermissions = Permission::where('name', 'like', 'pranota-rit%')->get();

echo "\n🔍 Found " . $pranotaRitPermissions->count() . " pranota rit permissions:\n";
foreach ($pranotaRitPermissions as $perm) {
    echo "   • {$perm->name} (ID: {$perm->id})\n";
}

// Get admin's current permissions for these modules
$adminCurrentPermissions = $admin->permissions()->pluck('name')->toArray();
$missingPermissions = [];

foreach ($pranotaRitPermissions as $perm) {
    if (!in_array($perm->name, $adminCurrentPermissions)) {
        $missingPermissions[] = $perm->id;
    }
}

echo "\n📊 Admin Permission Status:\n";
echo "   • Current permissions: " . count($adminCurrentPermissions) . "\n";
echo "   • Missing pranota rit permissions: " . count($missingPermissions) . "\n";

// Add missing permissions
if (!empty($missingPermissions)) {
    $admin->permissions()->attach($missingPermissions);
    echo "\n✅ Added " . count($missingPermissions) . " missing pranota rit permissions to admin user\n";
} else {
    echo "\n→ Admin already has all pranota rit permissions\n";
}

// Final verification
$finalPermissionCount = $admin->permissions()->count();
$finalPranotaRitPerms = $admin->permissions()
    ->where('name', 'like', 'pranota-rit%')
    ->orderBy('name')
    ->get();

echo "\n🎯 Final Status:\n";
echo "   • Admin total permissions: {$finalPermissionCount}\n";
echo "   • Admin pranota rit permissions: " . $finalPranotaRitPerms->count() . "/" . $pranotaRitPermissions->count() . "\n";

echo "\n📝 Admin Pranota Rit Permissions:\n";
foreach ($finalPranotaRitPerms as $perm) {
    echo "   ✅ {$perm->name}\n";
}

echo "\n🎉 Admin user now has complete access to Pranota Rit management!\n";

?>