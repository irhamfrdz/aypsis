<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Simulate user login
$user = User::find(1);
Auth::login($user);

echo '🧪 TESTING SIDEBAR MENU VISIBILITY' . PHP_EOL;
echo '=====================================' . PHP_EOL;
echo PHP_EOL;

echo '👤 Current User: ' . $user->username . PHP_EOL;
echo '🔢 User ID: ' . $user->id . PHP_EOL;
echo PHP_EOL;

// Test permission checks that would be used in sidebar
echo '🔍 Testing permission checks for sidebar:' . PHP_EOL;

// Pranota Supir menu
$pranotaView = $user->can('pranota-supir.index') || $user->hasPermissionTo('pranota-supir-view');
$pranotaCreate = $user->can('pranota-supir.create') || $user->hasPermissionTo('pranota-supir-create');

echo '📋 Pranota Supir Menu:' . PHP_EOL;
echo '  View Permission: ' . ($pranotaView ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  Create Permission: ' . ($pranotaCreate ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  Menu should be visible: ' . (($pranotaView || $pranotaCreate) ? '✅ YES' : '❌ NO') . PHP_EOL;
echo PHP_EOL;

// Pembayaran Pranota Supir menu
$pembayaranView = $user->can('pembayaran-pranota-supir.index') || $user->hasPermissionTo('pembayaran-pranota-supir-view');
$pembayaranCreate = $user->can('pembayaran-pranota-supir.create') || $user->hasPermissionTo('pembayaran-pranota-supir-create');

echo '💰 Pembayaran Pranota Supir Menu:' . PHP_EOL;
echo '  View Permission: ' . ($pembayaranView ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  Create Permission: ' . ($pembayaranCreate ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  Menu should be visible: ' . (($pembayaranView || $pembayaranCreate) ? '✅ YES' : '❌ NO') . PHP_EOL;
echo PHP_EOL;

// Test hasPermissionLike method
echo '🔍 Testing hasPermissionLike method:' . PHP_EOL;
echo '  hasPermissionLike("pranota-supir"): ' . ($user->hasPermissionLike('pranota-supir') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  hasPermissionLike("pembayaran-pranota-supir"): ' . ($user->hasPermissionLike('pembayaran-pranota-supir') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo PHP_EOL;

// Test middleware simulation
echo '🔍 Testing middleware simulation:' . PHP_EOL;
echo '  Permission-like middleware for pranota-supir: ' . ($user->hasPermissionLike('pranota-supir') ? '✅ ALLOW' : '❌ BLOCK') . PHP_EOL;
echo '  Permission-like middleware for pembayaran-pranota-supir: ' . ($user->hasPermissionLike('pembayaran-pranota-supir') ? '✅ ALLOW' : '❌ BLOCK') . PHP_EOL;
echo PHP_EOL;

// Summary
echo '📊 SUMMARY:' . PHP_EOL;
echo '==========' . PHP_EOL;

$pranotaVisible = $pranotaView || $pranotaCreate;
$pembayaranVisible = $pembayaranView || $pembayaranCreate;

echo '📋 Pranota Supir Menu: ' . ($pranotaVisible ? '✅ SHOULD BE VISIBLE' : '❌ WILL BE HIDDEN') . PHP_EOL;
echo '💰 Pembayaran Pranota Supir Menu: ' . ($pembayaranVisible ? '✅ SHOULD BE VISIBLE' : '❌ WILL BE HIDDEN') . PHP_EOL;

if ($pranotaVisible && $pembayaranVisible) {
    echo PHP_EOL;
    echo '🎉 SUCCESS: Both menus should now be visible in the sidebar!' . PHP_EOL;
    echo '🔗 You can now access:' . PHP_EOL;
    echo '   - /pranota-supir (Daftar Pranota Supir)' . PHP_EOL;
    echo '   - /pranota-supir/create (Buat Pranota Supir)' . PHP_EOL;
    echo '   - /pembayaran-pranota-supir (Pembayaran Pranota Supir)' . PHP_EOL;
} else {
    echo PHP_EOL;
    echo '⚠️  WARNING: Some menus may still be hidden. Check sidebar logic.' . PHP_EOL;
}

echo PHP_EOL;
echo '✅ Test completed!' . PHP_EOL;
