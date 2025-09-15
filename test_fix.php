<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== TESTING FIX FOR USER TEST2 ===\n";

$u = User::where('username', 'test2')->with('permissions')->first();

if (!$u) {
    echo "❌ User 'test2' not found!\n";
    exit(1);
}

echo "✅ User found: {$u->name} (ID: {$u->id})\n\n";

$perms = $u->permissions;
echo "📋 PRANOTA PERMISSIONS:\n";
foreach ($perms as $p) {
    if (strpos($p->name, 'pranota') !== false) {
        echo "  ✓ {$p->name}\n";
    }
}

echo "\n🎯 SIDEBAR LOGIC TEST:\n";

// Check conditions
$isAdmin = $u && method_exists($u, 'hasRole') && $u->hasRole('admin');
$canMasterPranotaSupir = $u->hasPermissionTo('master-pranota-supir');
$hasPranotaLike = $u->hasPermissionLike('pranota-supir');

echo "Is Admin: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n";
echo "Has 'master-pranota-supir': " . ($canMasterPranotaSupir ? '✅ YES' : '❌ NO') . "\n";
echo "Has permission like 'pranota-supir': " . ($hasPranotaLike ? '✅ YES' : '❌ NO') . "\n";

$shouldShowMenu = $isAdmin || $canMasterPranotaSupir || $hasPranotaLike;
echo "\n📊 RESULT:\n";
echo "Should pranota menu show: " . ($shouldShowMenu ? '✅ YES - FIXED!' : '❌ NO') . "\n";

if ($shouldShowMenu) {
    echo "\n🎉 SUCCESS! User test2 can now see the pranota menu in sidebar.\n";
    echo "The menu will appear because: " . ($hasPranotaLike ? "hasPermissionLike('pranota-supir') returns true" : "other condition met") . "\n";
} else {
    echo "\n❌ Still not working. Need further investigation.\n";
}
