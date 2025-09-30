<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing menu visibility for user marlina\n";
echo "=======================================\n\n";

// Find user marlina
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "❌ User marlina not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Test menu visibility logic (simulating app.blade.php)
$hasPranotaPerbaikanKontainerView = $user && $user->can('pranota-perbaikan-kontainer-view');

echo "Menu Visibility Test:\n";
echo "  - Pranota Perbaikan Kontainer menu visible: " . ($hasPranotaPerbaikanKontainerView ? "✅ YES" : "❌ NO") . "\n";

if ($hasPranotaPerbaikanKontainerView) {
    echo "\n✅ SUCCESS: Menu should now be visible for user marlina\n";
} else {
    echo "\n❌ FAILED: Menu will not be visible\n";
}

echo "\nRelevant permissions:\n";
foreach ($user->permissions as $perm) {
    if (strpos($perm->name, 'pranota-perbaikan-kontainer') !== false) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
}
