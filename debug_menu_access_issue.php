<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking User test4 Menu Access Issue\n";
echo "=====================================\n\n";

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
    'tagihan-kontainer-update',
    'tagihan-kontainer-delete',
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

echo "\nMenu Access Analysis (PROBLEM IDENTIFIED):\n";

// Check if user can see the main menu (this is the problem!)
$canSeeMainMenu = $user->hasPermissionTo('tagihan-kontainer-view');
echo "  - Can see 'Tagihan Kontainer Sewa' main menu: " . ($canSeeMainMenu ? "✅ YES (PROBLEM!)" : "❌ NO") . "\n";

// Check individual submenu access
$canAccessDaftarTagihan = $user->hasPermissionTo('tagihan-kontainer-view');
$canAccessPranota = $user->hasPermissionTo('pranota.view');
$canAccessPembayaranPranota = $user->hasPermissionTo('pembayaran-pranota-kontainer.view');

echo "  - Can access 'Daftar Tagihan Kontainer Sewa': " . ($canAccessDaftarTagihan ? "✅ YES" : "❌ NO") . "\n";
echo "  - Can access 'Daftar Pranota': " . ($canAccessPranota ? "✅ YES" : "❌ NO") . "\n";
echo "  - Can access 'Pembayaran Pranota Kontainer': " . ($canAccessPembayaranPranota ? "✅ YES" : "❌ NO") . "\n";

echo "\nROOT CAUSE ANALYSIS:\n";
echo "🔍 PROBLEM: The main menu 'Tagihan Kontainer Sewa' is shown based on 'tagihan-kontainer-view' permission\n";
echo "🔍 ISSUE: This allows view-only users to see menu items they shouldn't access\n";
echo "🔍 IMPACT: User test4 can navigate to pranota-related pages even with view-only permission\n";

echo "\nCurrent Menu Logic (from app.blade.php):\n";
echo "  @if(\$isAdmin || auth()->user()->can('tagihan-kontainer-view'))\n";
echo "    // Show entire 'Tagihan Kontainer Sewa' menu\n";
echo "  @endif\n";

echo "\nRecommended Fix:\n";
echo "  ✅ Change menu visibility logic to be more granular\n";
echo "  ✅ Show menu only if user has relevant permissions for the content\n";
echo "  ✅ Or hide specific submenu items based on permissions\n";

echo "\nTest completed!\n";
