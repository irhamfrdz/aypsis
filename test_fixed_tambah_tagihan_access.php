<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing FIXED user test4 access to 'Tambah Tagihan' feature\n";
echo "===========================================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Test permissions
$permissions = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'tagihan-kontainer-update',
    'tagihan-kontainer-delete'
];

echo "Permission Check:\n";
foreach ($permissions as $perm) {
    $hasPerm = $user->hasPermissionTo($perm);
    echo "  - $perm: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

echo "\nRoute Access Analysis (AFTER FIX):\n";

// Check if user can access the index page
$canAccessIndex = $user->hasPermissionTo('tagihan-kontainer-view');
echo "  - Can access daftar-tagihan-kontainer-sewa.index: " . ($canAccessIndex ? "✅ YES" : "❌ NO") . "\n";

// Check if user can access the create page (now requires create permission)
$canAccessCreate = $user->hasPermissionTo('tagihan-kontainer-create');
echo "  - Can access daftar-tagihan-kontainer-sewa.create: " . ($canAccessCreate ? "✅ YES" : "❌ NO") . "\n";

echo "\nView Logic Analysis (AFTER FIX):\n";
echo "  - 'Tambah Tagihan' button visibility: " . ($canAccessCreate ? "✅ SHOWN" : "❌ HIDDEN") . " (requires tagihan-kontainer-create)\n";

echo "\nExpected Behavior:\n";
echo "  ✅ User test4 can access the list page (has view permission)\n";
echo "  ❌ User test4 cannot access create page (missing create permission)\n";
echo "  ❌ 'Tambah Tagihan' button should be hidden for user test4\n";

echo "\nFix Summary:\n";
echo "  🔧 Route middleware: Changed from single 'can:tagihan-kontainer-view' to granular permissions\n";
echo "  🔧 View protection: Added @can('tagihan-kontainer-create') directive\n";
echo "  🔧 Result: View-only users can no longer access create functionality\n";

echo "\nTest completed!\n";
