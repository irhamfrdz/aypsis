<?php

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== CHECK TAGIHAN KONTAINER SEWA PERMISSIONS FOR MARLINA ===\n\n";

// Get user marlina
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "User marlina not found!\n";
    exit(1);
}

echo "User: {$user->name} (ID: {$user->id})\n\n";

// Check if user has tagihan-kontainer-sewa-index permission
$hasTagihanPermission = $user->can('tagihan-kontainer-sewa-index');
echo "Has 'tagihan-kontainer-sewa-index' permission: " . ($hasTagihanPermission ? 'YES' : 'NO') . "\n";

// Get all tagihan permissions for this user
$userPermissions = $user->getAllPermissions();
echo "\nAll tagihan-related permissions:\n";
foreach ($userPermissions as $perm) {
    if (strpos($perm->name, 'tagihan') !== false) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
}

echo "\n=== CONCLUSION ===\n";
if ($hasTagihanPermission) {
    echo "✓ User should see the menu\n";
} else {
    echo "✗ User will NOT see the menu - permission issue\n";
}
