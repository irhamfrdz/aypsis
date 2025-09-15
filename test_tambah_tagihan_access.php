<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing user test4 access to 'Tambah Tagihan' feature\n";
echo "=====================================================\n\n";

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

echo "\nRoute Access Analysis:\n";

// Check if user can access the index page (this is protected by tagihan-kontainer-view)
$canAccessIndex = $user->hasPermissionTo('tagihan-kontainer-view');
echo "  - Can access daftar-tagihan-kontainer-sewa.index: " . ($canAccessIndex ? "✅ YES" : "❌ NO") . "\n";

// Check if user can access the create page (this is also protected by tagihan-kontainer-view due to resource middleware)
$canAccessCreate = $user->hasPermissionTo('tagihan-kontainer-view');
echo "  - Can access daftar-tagihan-kontainer-sewa.create: " . ($canAccessCreate ? "✅ YES (PROBLEM!)" : "❌ NO") . "\n";

echo "\nView Logic Analysis:\n";
echo "  - 'Tambah Tagihan' button visibility: Always shown (no permission check in view)\n";
echo "  - Button links to: daftar-tagihan-kontainer-sewa.create\n";
echo "  - Route middleware: can:tagihan-kontainer-view (allows access)\n";

echo "\nRoot Cause:\n";
echo "  ❌ Route resource uses single middleware 'can:tagihan-kontainer-view' for ALL actions\n";
echo "  ❌ This allows view-only users to access create, edit, delete routes\n";
echo "  ❌ View doesn't check permission before showing 'Tambah Tagihan' button\n";

echo "\nSolutions:\n";
echo "  1. 🔧 Fix route middleware to use granular permissions per action\n";
echo "  2. 🔧 Add permission check in view for 'Tambah Tagihan' button\n";
echo "  3. 🔧 Or both approaches combined\n";

echo "\nTest completed!\n";
