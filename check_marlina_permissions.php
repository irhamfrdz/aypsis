<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Permissions for User Marlina\n";
echo "=====================================\n\n";

// Find user marlina
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "❌ User marlina not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// List all permissions for marlina
echo "All permissions for marlina:\n";
foreach($user->permissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// Check specific permissions
$permissionsToCheck = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-sewa-index', // The correct permission name
    'tagihan-kontainer.view', // Alternative format
    'perbaikan-kontainer-view', // Added for perbaikan kontainer access
];

echo "Specific permission checks:\n";
foreach ($permissionsToCheck as $perm) {
    $hasPerm = $user->hasPermissionTo($perm);
    echo "  - $perm: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

echo "\nMenu Visibility Check:\n";
// Simulate menu logic from app.blade.php
$hasAktivitasKontainerPermissions = $user && (
    $user->can('tagihan-kontainer-sewa-index') ||
    $user->can('pranota.view') ||
    $user->can('perbaikan-kontainer-view') ||
    $user->can('pranota-perbaikan-kontainer-view') ||
    $user->can('tagihan-cat-view') ||
    $user->can('pranota-cat-view')
);

$hasTagihanKontainerView = $user && $user->can('tagihan-kontainer-sewa-index');

echo "  - Aktivitas Kontainer menu visible: " . ($hasAktivitasKontainerPermissions ? "✅ YES" : "❌ NO") . "\n";
echo "  - Tagihan Kontainer Sewa item visible: " . ($hasTagihanKontainerView ? "✅ YES" : "❌ NO") . "\n";

?>
