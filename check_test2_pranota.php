<?php
// Quick check for user test2 permissions
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$username = 'test2';
$u = User::where('username', $username)->first();

if (!$u) {
    echo "USER_NOT_FOUND\n";
    exit(0);
}

echo "=== USER TEST2 PERMISSION ANALYSIS ===\n";
echo "User: {$u->name} ({$u->username})\n";
echo "ID: {$u->id}\n\n";

$perms = $u->permissions()->pluck('name')->toArray();
echo "PERMISSIONS (" . count($perms) . " total):\n";
foreach ($perms as $p) {
    echo "- $p\n";
}
echo "\n";

// Check specific permissions related to pranota
$pranotaRelated = array_filter($perms, function($p) {
    return strpos($p, 'pranota') !== false ||
           strpos($p, 'master-pranota') !== false ||
           strpos($p, 'tagihan-kontainer') !== false;
});

echo "PRANOTA-RELATED PERMISSIONS:\n";
if (empty($pranotaRelated)) {
    echo "❌ No pranota-related permissions found!\n";
} else {
    foreach ($pranotaRelated as $p) {
        echo "✓ $p\n";
    }
}
echo "\n";

// Check sidebar conditions
$gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
$user = $u;

echo "SIDEBAR MENU CHECKS:\n";

// Check if user is admin
$isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
echo "Is Admin: " . ($isAdmin ? 'YES' : 'NO') . "\n";

// Check specific gate for pranota menu
$canMasterPranotaSupir = $gate->forUser($user)->check('master-pranota-supir');
echo "Can 'master-pranota-supir': " . ($canMasterPranotaSupir ? 'YES' : 'NO') . "\n";

// Check hasPermissionLike for tagihan
$hasTagihanLike = $user->hasPermissionLike('tagihan-kontainer-sewa');
echo "Has permission like 'tagihan-kontainer-sewa': " . ($hasTagihanLike ? 'YES' : 'NO') . "\n";

// Check if pranota menu should show
$shouldShowPranotaMenu = $isAdmin || $canMasterPranotaSupir;
echo "\nPRANOTA MENU VISIBILITY:\n";
echo "Should show: " . ($shouldShowPranotaMenu ? 'YES' : 'NO') . "\n";

if (!$shouldShowPranotaMenu) {
    echo "\n❌ PROBLEM IDENTIFIED:\n";
    echo "User test2 does NOT have the required permission 'master-pranota-supir'\n";
    echo "and is not an admin. That's why the pranota menu doesn't show.\n\n";

    echo "SOLUTION OPTIONS:\n";
    echo "1. Assign permission 'master-pranota-supir' to user test2\n";
    echo "2. Or assign a permission that starts with 'pranota-'\n";
    echo "3. Or make user test2 an admin\n";
    echo "4. Or modify sidebar logic to check for user's existing permissions\n";
} else {
    echo "\n✅ Menu should be visible. Check if there are other issues.\n";
}
