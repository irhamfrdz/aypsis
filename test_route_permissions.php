<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo '❌ User test4 not found' . PHP_EOL;
    exit;
}

echo '=== TESTING PERMOHONAN ROUTE ACCESS FOR USER TEST4 ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

echo 'Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;

// Test route access by checking middleware
$routes = [
    'permohonan.index' => 'permohonan.index',
    'permohonan.create' => 'permohonan.create',
    'permohonan.store' => 'permohonan.create',
    'permohonan.show' => 'permohonan.index',
    'permohonan.edit' => 'permohonan.edit',
    'permohonan.update' => 'permohonan.edit',
    'permohonan.destroy' => 'permohonan.delete',
];

Auth::login($user);

echo '=== ROUTE ACCESS TEST ===' . PHP_EOL;
foreach ($routes as $routeName => $permission) {
    $hasAccess = $user->hasPermissionTo($permission);
    echo $routeName . ': ' . ($hasAccess ? '✅ ACCESSIBLE' : '❌ BLOCKED') . ' (requires: ' . $permission . ')' . PHP_EOL;
}

echo PHP_EOL;
echo '=== GATE CHECKS (with Auth::login) ===' . PHP_EOL;
Auth::login($user);
foreach ($routes as $routeName => $permission) {
    $hasAccess = \Illuminate\Support\Facades\Gate::allows($permission);
    echo $routeName . ': ' . ($hasAccess ? '✅ ACCESSIBLE' : '❌ BLOCKED') . ' (requires: ' . $permission . ')' . PHP_EOL;
}
Auth::logout();

Auth::logout();

echo PHP_EOL;
echo '=== SUMMARY ===' . PHP_EOL;
Auth::login($user);
$canCreate = \Illuminate\Support\Facades\Gate::allows('permohonan.create');
$canIndex = \Illuminate\Support\Facades\Gate::allows('permohonan.index');
Auth::logout();

echo 'Can access permohonan.index: ' . ($canIndex ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Can access permohonan.create: ' . ($canCreate ? '✅ YES' : '❌ NO') . PHP_EOL;

if ($canIndex && !$canCreate) {
    echo PHP_EOL . '✅ SUCCESS: User can view permohonan but cannot create new ones' . PHP_EOL;
    echo '✅ Permission system is now working correctly' . PHP_EOL;
    echo '✅ User test4 should be blocked from accessing /permohonan/create' . PHP_EOL;
} elseif ($canIndex && $canCreate) {
    echo PHP_EOL . '⚠️  WARNING: User can both view and create permohonan' . PHP_EOL;
    echo '⚠️  This might be intentional if user should have create permission' . PHP_EOL;
} else {
    echo PHP_EOL . '❌ ERROR: User cannot access permohonan at all' . PHP_EOL;
    echo '❌ Check if user has proper permissions' . PHP_EOL;
}
