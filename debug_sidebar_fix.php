<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== TESTING SIDEBAR LOGIC FIX ===\n\n";

// Get user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "User test4 found (ID: {$user->id})\n";

// Check current permissions
$user->refresh();
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions: " . implode(', ', $currentPermissions) . "\n\n";

// Simulate sidebar logic (old vs new)
$isAdmin = false; // Assuming test4 is not admin

echo "=== OLD SIDEBAR LOGIC ===\n";
$oldPranotaVisible = $isAdmin || $user->hasPermissionTo('pranota-supir');
$oldPembayaranPranotaVisible = $isAdmin || $user->hasPermissionTo('pembayaran-pranota-supir');

echo "Pranota Supir menu visible (old): " . ($oldPranotaVisible ? 'YES' : 'NO') . "\n";
echo "Pembayaran Pranota Supir menu visible (old): " . ($oldPembayaranPranotaVisible ? 'YES' : 'NO') . "\n\n";

echo "=== NEW SIDEBAR LOGIC ===\n";
$newPranotaVisible = $isAdmin ||
    $user->hasPermissionTo('pranota-supir') ||
    $user->hasPermissionTo('pranota-supir-view') ||
    $user->hasPermissionTo('pranota-supir-create') ||
    $user->hasPermissionTo('pranota-supir-update') ||
    $user->hasPermissionTo('pranota-supir-delete') ||
    $user->hasPermissionTo('pranota-supir-approve') ||
    $user->hasPermissionTo('pranota-supir-print') ||
    $user->hasPermissionTo('pranota-supir-export');

$newPembayaranPranotaVisible = $isAdmin ||
    $user->hasPermissionTo('pembayaran-pranota-supir') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-view') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-create') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-update') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-delete') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-approve') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-print') ||
    $user->hasPermissionTo('pembayaran-pranota-supir-export');

echo "Pranota Supir menu visible (new): " . ($newPranotaVisible ? 'YES' : 'NO') . "\n";
echo "Pembayaran Pranota Supir menu visible (new): " . ($newPembayaranPranotaVisible ? 'YES' : 'NO') . "\n\n";

echo "=== COMPARISON ===\n";
echo "Pranota Supir: " . ($oldPranotaVisible ? 'YES' : 'NO') . " → " . ($newPranotaVisible ? 'YES' : 'NO') . " " . ($oldPranotaVisible !== $newPranotaVisible ? '✅ FIXED' : '❌ NO CHANGE') . "\n";
echo "Pembayaran Pranota Supir: " . ($oldPembayaranPranotaVisible ? 'YES' : 'NO') . " → " . ($newPembayaranPranotaVisible ? 'YES' : 'NO') . " " . ($oldPembayaranPranotaVisible !== $newPembayaranPranotaVisible ? '✅ FIXED' : '❌ NO CHANGE') . "\n\n";

if ($newPranotaVisible) {
    echo "✅ SUCCESS: Menu Pranota Supir should now be visible in sidebar!\n";
} else {
    echo "❌ FAILURE: Menu Pranota Supir will still not be visible\n";
}

echo "\n=== INDIVIDUAL PERMISSION CHECKS ===\n";
$pranotaPermissions = [
    'pranota-supir',
    'pranota-supir-view',
    'pranota-supir-create',
    'pranota-supir-update',
    'pranota-supir-delete',
    'pranota-supir-approve',
    'pranota-supir-print',
    'pranota-supir-export'
];

foreach ($pranotaPermissions as $perm) {
    $hasPerm = $user->hasPermissionTo($perm);
    echo "hasPermissionTo('$perm'): " . ($hasPerm ? 'YES' : 'NO') . "\n";
}

?>
