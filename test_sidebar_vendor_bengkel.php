<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SIDEBAR CONDITION TEST FOR VENDOR/BENGKEL ===\n";

// Get admin user
$user = User::where('email', 'admin@admin.com')->first();
if (!$user) {
    echo "❌ Admin user not found\n";
    exit(1);
}

echo "User: {$user->name} ({$user->email})\n";
echo "Is admin: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";

// Test the actual conditions used in sidebar
$hasKontainerPermissions = $user && (
    $user->can('master-kontainer-view') ||
    $user->can('master-pricelist-sewa-kontainer-view') ||
    $user->can('master-stock-kontainer-view') ||
    $user->can('master-pricelist-cat-view') ||
    $user->can('master-vendor-bengkel.view')
);

$canViewVendorBengkel = $user && $user->can('master-vendor-bengkel.view');

echo "\n=== SIDEBAR CONDITIONS ===\n";
echo "\$hasKontainerPermissions: " . ($hasKontainerPermissions ? 'YES' : 'NO') . "\n";
echo "\$user->can('master-vendor-bengkel.view'): " . ($canViewVendorBengkel ? 'YES' : 'NO') . "\n";

echo "\n=== CONCLUSION ===\n";
if ($hasKontainerPermissions && $canViewVendorBengkel) {
    echo "✅ Menu Vendor/Bengkel WILL be visible in sidebar\n";
} else {
    echo "❌ Menu Vendor/Bengkel will NOT be visible in sidebar\n";
    if (!$hasKontainerPermissions) {
        echo "   Reason: \$hasKontainerPermissions is false\n";
    }
    if (!$canViewVendorBengkel) {
        echo "   Reason: User doesn't have master-vendor-bengkel.view permission\n";
    }
}
