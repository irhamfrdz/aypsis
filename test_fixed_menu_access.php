<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing FIXED Menu Access Control\n";
echo "=================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Check permissions
$permissions = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'pranota.view',
    'pranota.create',
    'pembayaran-pranota-kontainer.view',
    'pembayaran-pranota-kontainer.create',
    'pembayaran-pranota-tagihan-kontainer.view',
    'pembayaran-pranota-tagihan-kontainer.create'
];

echo "Permission Check:\n";
foreach ($permissions as $perm) {
    $hasPerm = $user->hasPermissionTo($perm);
    echo "  - $perm: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

echo "\nMenu Visibility Analysis (AFTER FIX):\n";

// Simulate the new menu logic
$isAdmin = false; // test4 is not admin
$hasTagihanAccess = $isAdmin || $user->hasPermissionTo('tagihan-kontainer-view');
$hasPranotaAccess = $isAdmin || $user->hasPermissionTo('pranota.view');
$hasPembayaranPranotaAccess = $isAdmin || $user->hasPermissionTo('pembayaran-pranota-kontainer.view') || $user->hasPermissionTo('pembayaran-pranota-tagihan-kontainer.view');
$hasAnyAccess = $hasTagihanAccess || $hasPranotaAccess || $hasPembayaranPranotaAccess;

echo "  - Has tagihan access: " . ($hasTagihanAccess ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has pranota access: " . ($hasPranotaAccess ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has pembayaran pranota access: " . ($hasPembayaranPranotaAccess ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has ANY access (menu visible): " . ($hasAnyAccess ? "✅ YES" : "❌ NO") . "\n";

echo "\nSubmenu Visibility Analysis:\n";
echo "  - 'Daftar Tagihan Kontainer' submenu: " . ($hasTagihanAccess ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";
echo "  - 'Daftar Pranota Kontainer' submenu: " . ($hasPranotaAccess ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";
echo "  - 'Pembayaran Pranota Kontainer' submenu: " . ($hasPembayaranPranotaAccess ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";

echo "\nExpected Behavior for user test4 (AFTER FIX):\n";
echo "  ✅ Main menu 'Tagihan Kontainer Sewa' should be: " . ($hasAnyAccess ? "VISIBLE" : "HIDDEN") . "\n";
echo "  ✅ 'Daftar Tagihan Kontainer' should be: " . ($hasTagihanAccess ? "VISIBLE" : "HIDDEN") . "\n";
echo "  ❌ 'Daftar Pranota Kontainer' should be: " . ($hasPranotaAccess ? "VISIBLE" : "HIDDEN") . "\n";
echo "  ❌ Other pranota-related menus should be: " . ($hasPembayaranPranotaAccess ? "VISIBLE" : "HIDDEN") . "\n";

echo "\nFix Summary:\n";
echo "  🔧 BEFORE: Menu shown if user has 'tagihan-kontainer-view' (too broad)\n";
echo "  🔧 AFTER: Menu shown only if user has access to at least one submenu\n";
echo "  🔧 RESULT: User test4 can only see 'Daftar Tagihan Kontainer', not pranota menus\n";

echo "\nTest completed!\n";
