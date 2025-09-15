<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Get current user or first user
$user = Auth::user() ?? User::first();

if (!$user) {
    echo '❌ No user found' . PHP_EOL;
    exit;
}

echo '👤 Current User: ' . $user->username . PHP_EOL;
echo '📧 Email: ' . ($user->email ?? 'N/A') . PHP_EOL;
echo '🔢 User ID: ' . $user->id . PHP_EOL;
echo PHP_EOL;

echo '🔍 Checking permissions for pranota-supir:' . PHP_EOL;
$pranotaPermissions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
foreach ($pranotaPermissions as $action) {
    $permName = 'pranota-supir-' . $action;
    $hasPermission = $user->permissions->contains('name', $permName);
    echo '  ' . ($hasPermission ? '✅' : '❌') . ' ' . $permName . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Checking permissions for pembayaran-pranota-supir:' . PHP_EOL;
$pembayaranPermissions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
foreach ($pembayaranPermissions as $action) {
    $permName = 'pembayaran-pranota-supir-' . $action;
    $hasPermission = $user->permissions->contains('name', $permName);
    echo '  ' . ($hasPermission ? '✅' : '❌') . ' ' . $permName . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Checking hasPermissionLike method:' . PHP_EOL;
echo '  hasPermissionLike("pranota-supir"): ' . ($user->hasPermissionLike('pranota-supir') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  hasPermissionLike("pembayaran-pranota-supir"): ' . ($user->hasPermissionLike('pembayaran-pranota-supir') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo '📊 Total permissions: ' . $user->permissions->count() . PHP_EOL;
echo '📋 Permission names: ' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '  - ' . $perm->name . PHP_EOL;
}
