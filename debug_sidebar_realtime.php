<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== SIDEBAR DEBUG: Real-time Check ===\n";

$user = User::where('username', 'user_admin')->first();

if (!$user) {
    echo "❌ User 'user_admin' not found!\n";
    exit(1);
}

echo "✅ User found: {$user->username}\n";
echo "Status: {$user->status}\n";
echo "Auth check: " . (auth()->check() ? '✅ Logged in' : '❌ Not logged in') . "\n\n";

// Simulate EXACT sidebar logic from app.blade.php
echo "🔍 SIMULATING SIDEBAR LOGIC:\n";

// Step 1: Check hasRole method
$hasRoleMethod = method_exists($user, 'hasRole');
echo "1. hasRole method exists: " . ($hasRoleMethod ? '✅ Yes' : '❌ No') . "\n";

$isAdmin = $hasRoleMethod && $user->hasRole('admin');
echo "2. isAdmin (hasRole('admin')): " . ($isAdmin ? '✅ True' : '❌ False') . "\n";

// Step 2: Check master permissions (EXACT list from sidebar)
$masterPermissions = [
    'master-karyawan-view',
    'master-user-view',
    'master-kontainer-view',
    'master-pricelist-sewa-kontainer-view',
    'master-tujuan-view',
    'master-kegiatan-view',
    'master-permission-view',
    'master-mobil-view',
    'master-divisi-view',
    'master-cabang-view',
    'master-pekerjaan-view',
    'master-pajak-view',
    'master-bank-view',
    'master-coa-view'
];

$hasMasterPermissions = false;
$permissionsFound = [];

foreach ($masterPermissions as $perm) {
    if ($user->can($perm)) {
        $hasMasterPermissions = true;
        $permissionsFound[] = $perm;
    }
}

echo "3. hasMasterPermissions: " . ($hasMasterPermissions ? '✅ True' : '❌ False') . "\n";
echo "   Permissions found: " . count($permissionsFound) . " out of " . count($masterPermissions) . "\n";

if (!empty($permissionsFound)) {
    echo "   ✅ Found permissions:\n";
    foreach ($permissionsFound as $perm) {
        echo "      - $perm\n";
    }
}

// Step 3: Final logic
$showMasterSection = $isAdmin || $hasMasterPermissions;

echo "\n🎯 FINAL SIDEBAR RESULT:\n";
echo "showMasterSection = isAdmin OR hasMasterPermissions\n";
echo "showMasterSection = " . ($isAdmin ? 'true' : 'false') . " OR " . ($hasMasterPermissions ? 'true' : 'false') . "\n";
echo "showMasterSection = " . ($showMasterSection ? '✅ TRUE' : '❌ FALSE') . "\n";

if ($showMasterSection) {
    echo "\n🎉 SUCCESS: Master Data section SHOULD appear in sidebar!\n";
    echo "📋 What will be shown:\n";
    echo "   - Master Data section header\n";
    echo "   - Individual menu items based on permissions\n";
} else {
    echo "\n❌ PROBLEM: Master Data section will be HIDDEN!\n";
    echo "🔧 Possible solutions:\n";

    if (!$isAdmin && !$hasMasterPermissions) {
        echo "   - User has neither admin role nor master permissions\n";
        echo "   - Check if user_admin has the required permissions\n";
        echo "   - Check if role assignment is working\n";
    } elseif (!$isAdmin) {
        echo "   - User has permissions but no admin role\n";
        echo "   - This should still show Master Data (logic updated)\n";
    } elseif (!$hasMasterPermissions) {
        echo "   - User has admin role but no master permissions\n";
        echo "   - Check if permissions are properly assigned\n";
    }
}

echo "\n💡 DEBUG INFO:\n";
echo "- Total user permissions: " . $user->permissions()->count() . "\n";
echo "- User roles: " . $user->roles()->count() . "\n";

if ($user->roles()->count() > 0) {
    echo "- Role names: ";
    $roles = $user->roles()->pluck('name')->toArray();
    echo implode(', ', $roles) . "\n";
}
