<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Menu Access for User with NO Access\n";
echo "=============================================\n\n";

// Find a user with no relevant permissions
$users = User::all();
$userWithNoAccess = null;

foreach ($users as $user) {
    $hasTagihanAccess = $user->hasPermissionTo('tagihan-kontainer-view');
    $hasPranotaAccess = $user->hasPermissionTo('pranota.view');
    $hasPembayaranPranotaAccess = $user->hasPermissionTo('pembayaran-pranota-kontainer.view') || $user->hasPermissionTo('pembayaran-pranota-tagihan-kontainer.view');

    if (!$hasTagihanAccess && !$hasPranotaAccess && !$hasPembayaranPranotaAccess && !$user->hasRole('admin')) {
        $userWithNoAccess = $user;
        break;
    }
}

if (!$userWithNoAccess) {
    echo "❌ No user found with no access to tagihan/pranota menus\n";
    echo "Available users and their access:\n";
    foreach ($users as $user) {
        $hasTagihan = $user->hasPermissionTo('tagihan-kontainer-view');
        $hasPranota = $user->hasPermissionTo('pranota.view');
        $hasPembayaran = $user->hasPermissionTo('pembayaran-pranota-kontainer.view') || $user->hasPermissionTo('pembayaran-pranota-tagihan-kontainer.view');
        $isAdmin = $user->hasRole('admin');
        $hasAny = $hasTagihan || $hasPranota || $hasPembayaran || $isAdmin;

        echo "  - {$user->username}: Menu " . ($hasAny ? "VISIBLE" : "HIDDEN") . " (tagihan:$hasTagihan, pranota:$hasPranota, pembayaran:$hasPembayaran, admin:$isAdmin)\n";
    }
    exit(1);
}

echo "User with NO access: {$userWithNoAccess->username} (ID: {$userWithNoAccess->id})\n\n";

// Check permissions
$permissions = [
    'tagihan-kontainer-view',
    'pranota.view',
    'pembayaran-pranota-kontainer.view',
    'pembayaran-pranota-tagihan-kontainer.view'
];

echo "Permission Check:\n";
foreach ($permissions as $perm) {
    $hasPerm = $userWithNoAccess->hasPermissionTo($perm);
    echo "  - $perm: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

echo "\nMenu Visibility Analysis:\n";

// Simulate the new menu logic
$isAdmin = $userWithNoAccess->hasRole('admin');
$hasTagihanAccess = $isAdmin || $userWithNoAccess->hasPermissionTo('tagihan-kontainer-view');
$hasPranotaAccess = $isAdmin || $userWithNoAccess->hasPermissionTo('pranota.view');
$hasPembayaranPranotaAccess = $isAdmin || $userWithNoAccess->hasPermissionTo('pembayaran-pranota-kontainer.view') || $userWithNoAccess->hasPermissionTo('pembayaran-pranota-tagihan-kontainer.view');
$hasAnyAccess = $hasTagihanAccess || $hasPranotaAccess || $hasPembayaranPranotaAccess;

echo "  - Is admin: " . ($isAdmin ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has tagihan access: " . ($hasTagihanAccess ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has pranota access: " . ($hasPranotaAccess ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has pembayaran pranota access: " . ($hasPembayaranPranotaAccess ? "✅ YES" : "❌ NO") . "\n";
echo "  - Has ANY access (menu visible): " . ($hasAnyAccess ? "❌ UNEXPECTED (should be HIDDEN)" : "✅ HIDDEN (as expected)") . "\n";

echo "\nExpected Behavior:\n";
echo "  ❌ Main menu 'Tagihan Kontainer Sewa' should be: " . ($hasAnyAccess ? "VISIBLE" : "HIDDEN") . "\n";
echo "  ❌ All submenus should be: " . ($hasAnyAccess ? "VISIBLE" : "HIDDEN") . "\n";

if ($hasAnyAccess) {
    echo "\n⚠️  WARNING: Menu is still visible for user with no permissions!\n";
    echo "   This indicates the fix may not be working correctly.\n";
} else {
    echo "\n✅ SUCCESS: Menu is properly hidden for user with no permissions!\n";
}

echo "\nTest completed!\n";
