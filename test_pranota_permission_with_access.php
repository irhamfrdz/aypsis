<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Pranota Permission - User with Access\n";
echo "=============================================\n\n";

// Find a user with pranota permissions (try admin or first user)
$users = User::all();
$userWithPranota = null;

foreach ($users as $user) {
    if ($user->hasPermissionTo('pranota.create')) {
        $userWithPranota = $user;
        break;
    }
}

if (!$userWithPranota) {
    echo "❌ No user found with pranota-create permission\n";
    echo "Available users:\n";
    foreach ($users as $user) {
        echo "  - {$user->username} (ID: {$user->id})\n";
    }
    exit(1);
}

echo "User with pranota permission: {$userWithPranota->username} (ID: {$userWithPranota->id})\n\n";

// Test permissions
$permissions = [
    'pranota.create',
    'pranota.view',
    'pranota.update',
    'pranota.delete',
    'tagihan-kontainer-create',
    'tagihan-kontainer-view'
];

echo "Permission Check:\n";
foreach ($permissions as $perm) {
    $hasPerm = $userWithPranota->hasPermissionTo($perm);
    echo "  - $perm: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

echo "\nPranota Feature Access Analysis:\n";

// Check if user can access pranota creation features
$canCreatePranota = $userWithPranota->hasPermissionTo('pranota.create');
echo "  - Can create pranota: " . ($canCreatePranota ? "✅ YES" : "❌ NO") . "\n";

// Check if user can access tagihan features
$canCreateTagihan = $userWithPranota->hasPermissionTo('tagihan-kontainer-create');
echo "  - Can create tagihan: " . ($canCreateTagihan ? "✅ YES" : "❌ NO") . "\n";

echo "\nUI Behavior Analysis:\n";
echo "  - 'Buat Pranota Terpilih' button visibility: " . ($canCreatePranota ? "✅ SHOWN" : "❌ HIDDEN") . " (requires pranota-create)\n";
echo "  - Individual 'Pranota' button visibility: " . ($canCreatePranota ? "✅ SHOWN" : "❌ HIDDEN") . " (requires pranota-create)\n";
echo "  - JavaScript warning when clicking: " . (!$canCreatePranota ? "⚠️ ACTIVE" : "✅ BYPASSED") . "\n";

echo "\nExpected Behavior for user with pranota permission:\n";
echo "  ✅ User should see 'Buat Pranota Terpilih' button\n";
echo "  ✅ User should see individual 'Pranota' buttons\n";
echo "  ✅ User can access pranota creation features without warnings\n";

echo "\nComparison with user test4:\n";
echo "  🔄 test4: No pranota buttons visible, JavaScript warning active\n";
echo "  🔄 {$userWithPranota->username}: Pranota buttons visible, no JavaScript warning\n";

echo "\nTest completed!\n";
