<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing sidebar menu visibility for user test4\n";
echo "==============================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Test tagihan kontainer permission
$hasTagihanView = $user->hasPermissionTo('tagihan-kontainer-view');
echo "Has tagihan-kontainer-view permission: " . ($hasTagihanView ? "✅ YES" : "❌ NO") . "\n";

// Test other tagihan permissions
$tagihanPermissions = [
    'tagihan-kontainer-create',
    'tagihan-kontainer-update',
    'tagihan-kontainer-delete',
    'tagihan-kontainer-approve',
    'tagihan-kontainer-print',
    'tagihan-kontainer-export'
];

echo "\nOther tagihan permissions:\n";
foreach ($tagihanPermissions as $perm) {
    $hasPerm = $user->hasPermissionTo($perm);
    echo "  - $perm: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

echo "\nSidebar Menu Logic Test:\n";
// Simulate sidebar menu condition (based on what we saw in app.blade.php)
if ($hasTagihanView) {
    echo "✅ Tagihan Kontainer menu SHOULD appear in sidebar\n";
} else {
    echo "❌ Tagihan Kontainer menu will NOT appear in sidebar\n";
}

echo "\nTest completed!\n";
