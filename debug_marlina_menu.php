<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Debugging Menu Visibility for User Marlina\n";
echo "==========================================\n\n";

// Find user marlina
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "❌ User marlina not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Check if user has the correct permission
$hasTagihanPermission = $user->hasPermissionTo('tagihan-kontainer-sewa-index');
echo "Has 'tagihan-kontainer-sewa-index' permission: " . ($hasTagihanPermission ? "✅ YES" : "❌ NO") . "\n\n";

// Simulate the exact menu logic from app.blade.php
echo "Menu Logic Simulation:\n";
echo "---------------------\n";

$isAktivitasKontainerRoute = false; // We're not on that route for this test
$hasAktivitasKontainerPermissions = $user && (
    $user->can('tagihan-kontainer-sewa-index') ||
    $user->can('pranota.view') ||
    $user->can('perbaikan-kontainer-view') ||
    $user->can('pranota-perbaikan-kontainer-view') ||
    $user->can('tagihan-cat-view') ||
    $user->can('pranota-cat-view')
);

$hasTagihanKontainerView = $user && $user->can('tagihan-kontainer-sewa-index');

echo "- Aktivitas Kontainer menu should be visible: " . ($hasAktivitasKontainerPermissions ? "✅ YES" : "❌ NO") . "\n";
echo "- Tagihan Kontainer Sewa item should be visible: " . ($hasTagihanKontainerView ? "✅ YES" : "❌ NO") . "\n\n";

// Check if there are any caching issues
echo "Cache Check:\n";
echo "------------\n";
echo "If menu still doesn't show, try:\n";
echo "1. Clear application cache: php artisan cache:clear\n";
echo "2. Clear config cache: php artisan config:clear\n";
echo "3. Clear route cache: php artisan route:clear\n";
echo "4. Clear view cache: php artisan view:clear\n";
echo "5. Logout and login again for user marlina\n";
echo "6. Check browser cache (hard refresh)\n\n";

// Check permission details
echo "Permission Details:\n";
echo "-------------------\n";
$tagihanPerm = Permission::where('name', 'tagihan-kontainer-sewa-index')->first();
if ($tagihanPerm) {
    echo "Permission 'tagihan-kontainer-sewa-index' exists (ID: {$tagihanPerm->id})\n";
    $userHasIt = $user->permissions()->where('id', $tagihanPerm->id)->exists();
    echo "User has this permission: " . ($userHasIt ? "✅ YES" : "❌ NO") . "\n";
} else {
    echo "❌ Permission 'tagihan-kontainer-sewa-index' does not exist in database!\n";
}

?>
