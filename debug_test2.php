<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== CHECKING USER TEST2 PERMISSIONS ===\n";

$u = User::where('username', 'test2')->with('permissions')->first();

if (!$u) {
    echo "❌ User 'test2' not found in database!\n";
    exit(1);
}

echo "✅ User found: {$u->name} (ID: {$u->id})\n\n";

$perms = $u->permissions;
echo "📋 ALL PERMISSIONS ({$perms->count()} total):\n";

if ($perms->isEmpty()) {
    echo "❌ No permissions assigned to this user!\n";
} else {
    foreach ($perms as $p) {
        echo "  - {$p->name}\n";
    }
}

echo "\n🔍 PRANOTA-RELATED PERMISSIONS:\n";
$pranotaPerms = $perms->filter(function($p) {
    $name = $p->name;
    return strpos($name, 'pranota') !== false ||
           strpos($name, 'master-pranota') !== false ||
           strpos($name, 'tagihan-kontainer') !== false;
});

if ($pranotaPerms->isEmpty()) {
    echo "❌ No pranota-related permissions found!\n";
} else {
    foreach ($pranotaPerms as $p) {
        echo "  ✓ {$p->name}\n";
    }
}

echo "\n🎯 SIDEBAR MENU ANALYSIS:\n";

// Check if user is admin
$isAdmin = $u && method_exists($u, 'hasRole') && $u->hasRole('admin');
echo "Is Admin: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n";

// Check specific permission for pranota menu
$canMasterPranotaSupir = $u->hasPermissionTo('master-pranota-supir');
echo "Has 'master-pranota-supir': " . ($canMasterPranotaSupir ? '✅ YES' : '❌ NO') . "\n";

// Check hasPermissionLike for tagihan
$hasTagihanLike = $u->hasPermissionLike('tagihan-kontainer-sewa');
echo "Has permission like 'tagihan-kontainer-sewa': " . ($hasTagihanLike ? '✅ YES' : '❌ NO') . "\n";

// Final verdict
$shouldShowPranotaMenu = $isAdmin || $canMasterPranotaSupir;
echo "\n📊 CONCLUSION:\n";
echo "Should pranota menu show: " . ($shouldShowPranotaMenu ? '✅ YES' : '❌ NO') . "\n";

if (!$shouldShowPranotaMenu) {
    echo "\n🚨 PROBLEM IDENTIFIED:\n";
    echo "The pranota menu doesn't show because user test2:\n";
    echo "1. Is not an admin\n";
    echo "2. Does not have the permission 'master-pranota-supir'\n";
    echo "3. Does not have permissions that start with 'pranota-'\n\n";

    echo "💡 SOLUTIONS:\n";
    echo "1. Assign permission 'master-pranota-supir' to user test2\n";
    echo "2. Or assign a permission that starts with 'pranota-'\n";
    echo "3. Or modify the sidebar logic to check for existing permissions\n";
} else {
    echo "\n✅ The menu should be visible. Check for other issues.\n";
}
