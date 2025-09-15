<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo '=== FINAL VERIFICATION: PERMOHONAN PERMISSION SYSTEM ===' . PHP_EOL;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo '❌ User test4 not found' . PHP_EOL;
    exit;
}

echo 'User: ' . $user->username . PHP_EOL;
echo 'Email: ' . $user->email . PHP_EOL;
echo PHP_EOL;

// Test 1: Check direct permission methods
echo '=== TEST 1: DIRECT PERMISSION METHODS ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan"): ' . ($user->hasPermissionTo('permohonan') ? '✅ TRUE' : '❌ FALSE') . PHP_EOL;
echo 'hasPermissionTo("permohonan.index"): ' . ($user->hasPermissionTo('permohonan.index') ? '✅ TRUE' : '❌ FALSE') . PHP_EOL;
echo PHP_EOL;

// Test 2: Check Gate can() method
echo '=== TEST 2: GATE CAN() METHOD ===' . PHP_EOL;
Auth::login($user);
echo 'Gate::allows("permohonan"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan') ? '✅ TRUE' : '❌ FALSE') . PHP_EOL;
echo 'Gate::allows("permohonan.index"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.index') ? '✅ TRUE' : '❌ FALSE') . PHP_EOL;
echo PHP_EOL;

// Test 3: Check all permohonan permissions
echo '=== TEST 3: ALL PERMOHONAN PERMISSIONS ===' . PHP_EOL;
$permohonanPermissions = [
    'permohonan',
    'permohonan.index',
    'permohonan.create',
    'permohonan.edit',
    'permohonan.delete'
];

foreach ($permohonanPermissions as $perm) {
    $hasPermission = $user->hasPermissionTo($perm);
    echo $perm . ': ' . ($hasPermission ? '✅' : '❌') . PHP_EOL;
}

echo PHP_EOL;
echo '=== SUMMARY ===' . PHP_EOL;
$canPermohonan = \Illuminate\Support\Facades\Gate::allows('permohonan');
$canPermohonanIndex = \Illuminate\Support\Facades\Gate::allows('permohonan.index');

if ($canPermohonan && $canPermohonanIndex) {
    echo '✅ SUCCESS: User test4 has both simple and detailed permohonan permissions' . PHP_EOL;
    echo '✅ Sidebar should display Permohonan Memo menu' . PHP_EOL;
    echo '✅ User can access permohonan pages' . PHP_EOL;
} elseif ($canPermohonanIndex && !$canPermohonan) {
    echo '⚠️  PARTIAL: User has detailed permissions but missing simple permohonan permission' . PHP_EOL;
    echo '⚠️  Sidebar may not display menu - needs simple permission' . PHP_EOL;
} else {
    echo '❌ FAILED: User missing required permissions' . PHP_EOL;
    echo '❌ Sidebar will not display Permohonan Memo menu' . PHP_EOL;
}

Auth::logout();
