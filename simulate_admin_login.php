<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

echo "=== SIMULATED ADMIN LOGIN TEST ===\n\n";

// Find admin user
$adminUser = User::where('username', 'admin')->first();
if (!$adminUser) {
    // Try to find any user with admin-like permissions
    $adminUser = User::whereHas('permissions', function($q) {
        $q->where('name', 'like', '%admin%');
    })->first();
}

if (!$adminUser) {
    echo "❌ No admin user found in database\n";
    echo "Available users:\n";
    $users = User::select('id', 'username')->get();
    foreach ($users as $user) {
        echo "- {$user->username} (ID: {$user->id})\n";
    }
    exit;
}

echo "✅ Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n";

// Simulate login
Auth::login($adminUser);
echo "✅ Simulated login successful\n";

echo "\n2. PERMISSIONS CHECK:\n";
// Check user's permissions
$userPermissions = $adminUser->permissions->pluck('name')->toArray();
echo "User has " . count($userPermissions) . " permissions\n";

$requiredPermissions = [
    'master-kode-nomor-view',
    'master-kode-nomor',
    'master-kode-nomor.view'
];

$hasRequiredPermission = false;
foreach ($requiredPermissions as $perm) {
    $hasPermission = in_array($perm, $userPermissions);
    echo ($hasPermission ? "✅" : "❌") . " Has permission: $perm\n";
    if ($hasPermission) $hasRequiredPermission = true;
}

echo "\n3. SIDEBAR LOGIC SIMULATION:\n";
// Simulate the sidebar logic from app.blade.php
$user = $adminUser;

// Check master permissions logic
$hasMasterPermissions = $user->can('master-karyawan-view') ||
                       $user->can('master-user-view') ||
                       $user->can('master-kontainer-view') ||
                       $user->can('master-tujuan-view') ||
                       $user->can('master-kegiatan-view') ||
                       $user->can('master-permission-view') ||
                       $user->can('master-mobil-view') ||
                       $user->can('master-divisi-view') ||
                       $user->can('master-pajak-view') ||
                       $user->can('master-pricelist-sewa-kontainer-view') ||
                       $user->can('master-bank-view') ||
                       $user->can('master-coa-view') ||
                       $user->can('master-vendor-bengkel-view') ||
                       $user->can('master-kode-nomor-view');

echo ($hasMasterPermissions ? "✅" : "❌") . " Has master permissions (should show master section)\n";

// Check if current route is master route
$isMasterRoute = false; // Since we're not on a specific route
echo "ℹ️  Is master route: Cannot determine without HTTP request\n";

// Check specific permission
$specificPermission = $user->can('master-kode-nomor-view');
echo ($specificPermission ? "✅" : "❌") . " Has master-kode-nomor-view permission\n";

echo "\n4. MENU VISIBILITY CONCLUSION:\n";
if ($hasMasterPermissions && $specificPermission) {
    echo "🎉 MENU 'KODE NOMOR' SHOULD APPEAR IN SIDEBAR!\n";
    echo "📍 Location: Inside 'Master Data' dropdown\n";
} else {
    echo "❌ Menu will NOT appear due to missing permissions\n";
    if (!$hasMasterPermissions) {
        echo "   - Missing master permissions\n";
    }
    if (!$specificPermission) {
        echo "   - Missing master-kode-nomor-view permission\n";
    }
}

echo "\n5. TROUBLESHOOTING STEPS FOR USER:\n";
echo "1. Make sure you're logged in as: {$adminUser->username}\n";
echo "2. Hard refresh browser (Ctrl+F5)\n";
echo "3. Clear browser cache\n";
echo "4. Check if 'Master Data' dropdown is visible\n";
echo "5. Click on 'Master Data' to expand the dropdown\n";
echo "6. Look for 'Kode Nomor' in the dropdown menu\n";

if (!$hasRequiredPermission) {
    echo "\n⚠️  WARNING: User missing required permissions!\n";
    echo "Run this to assign permissions:\n";
    echo "php artisan tinker\n";
    echo "\$user = User::find({$adminUser->id});\n";
    echo "\$perm = Permission::where('name', 'master-kode-nomor-view')->first();\n";
    echo "\$user->permissions()->attach(\$perm);\n";
}
