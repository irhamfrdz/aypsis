<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Adding Missing BL Permissions to Admin ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

// Get missing BL permissions
$missingPermissions = ['bl-view', 'bl-create', 'bl-edit'];
$adminCurrentPermissions = $admin->permissions()->pluck('name')->toArray();

$permissionsToAdd = [];
foreach ($missingPermissions as $permName) {
    if (!in_array($permName, $adminCurrentPermissions)) {
        $permission = Permission::where('name', $permName)->first();
        if ($permission) {
            $permissionsToAdd[] = $permission->id;
            echo "✓ Will add: {$permName} (ID: {$permission->id})\n";
        } else {
            echo "❌ Permission {$permName} not found in database\n";
        }
    } else {
        echo "→ Already has: {$permName}\n";
    }
}

// Add missing permissions
if (!empty($permissionsToAdd)) {
    $admin->permissions()->attach($permissionsToAdd);
    echo "\n✅ Added " . count($permissionsToAdd) . " missing BL permissions to admin user\n";
} else {
    echo "\n→ No missing permissions to add\n";
}

// Verify final state
$finalBLPermissions = $admin->permissions()
    ->where('name', 'like', 'bl-%')
    ->orderBy('name')
    ->get();

echo "\nFinal admin BL permissions (" . $finalBLPermissions->count() . " total):\n";
foreach ($finalBLPermissions as $perm) {
    echo "   ✓ {$perm->name}\n";
}

echo "\n=== Complete ===\n";

?>